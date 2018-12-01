<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\base\Field;
use craft\elements\User;
use craft\models\EntryType;
use craft\redactor\FieldData;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Language\AST\InterfaceTypeDefinitionNode;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Utils\SchemaPrinter;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use markhuot\CraftQL\Arguments\EntryQueryArguments;
use markhuot\CraftQL\Builders\InferredSchema;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Events\AlterQuerySchema;
use markhuot\CraftQL\Helpers\StringHelper;
use markhuot\CraftQL\TypeRegistry;
use markhuot\CraftQL\Types\Category;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\Globals;
use markhuot\CraftQL\Types\ProxyObject;
use markhuot\CraftQL\Types\Query;
use markhuot\CraftQL\Types\RedactorFieldData;
use markhuot\CraftQL\Types\Tag;
use yii\base\Component;

class GraphQLService extends Component {

    private $volumes;
    private $categoryGroups;
    private $tagGroups;
    private $entryTypes;
    private $sections;
    private $globals;

    function __construct(
        \markhuot\CraftQL\Repositories\Volumes $volumes,
        \markhuot\CraftQL\Repositories\CategoryGroup $categoryGroups,
        \markhuot\CraftQL\Repositories\TagGroup $tagGroups,
        \markhuot\CraftQL\Repositories\EntryType $entryTypes,
        \markhuot\CraftQL\Repositories\Section $sections,
        \markhuot\CraftQL\Repositories\Globals $globals
    ) {
        parent::__construct();
        $this->volumes = $volumes;
        $this->categoryGroups = $categoryGroups;
        $this->tagGroups = $tagGroups;
        $this->entryTypes = $entryTypes;
        $this->sections = $sections;
        $this->globals = $globals;
    }

    /**
     * Bootstrap the schema
     *
     * @return void
     */
    function bootstrap() {
        $maxQueryDepth = CraftQL::getInstance()->getSettings()->maxQueryDepth;
        if ($maxQueryDepth !== false) {
            $rule = new QueryDepth($maxQueryDepth);
            DocumentValidator::addRule($rule);
        }

        $maxQueryComplexity = CraftQL::getInstance()->getSettings()->maxQueryComplexity;
        if ($maxQueryComplexity !== false) {
            $rule = new QueryComplexity($maxQueryComplexity);
            DocumentValidator::addRule($rule);
        }
    }

    function getSchema($token) {
        $request = new \markhuot\CraftQL\Request($token);

        $registry = new TypeRegistry($request);
        $registry->registerNamespace('\\markhuot\\CraftQL\\Types');
        $request->addRegistry($registry);

        if (false && $schemaText = Craft::$app->cache->get('foo')) {
            $typeConfigDecorator = function ($type) use ($registry) {
                if (get_class($type['astNode']) == InterfaceTypeDefinitionNode::class) {
                    $fqen = $registry->getClassForName($type['name']);
                    $type['resolveType'] = function ($source) use ($fqen) {
                        // @TODO this is gross, needs to be cleaned up
                        if (is_subclass_of($source, ProxyObject::class)) {
                            $source = $source->getSource();
                        }
                        return $fqen::craftQLResolveType($source);
                    };
                }
                return $type;
            };
            $schema = BuildSchema::build(AST::fromArray(unserialize($schemaText)), $typeConfigDecorator);
            return [$request, $schema];
        }

        // now that the cached schema has been returned we know we're building a full schema so load
        // everything here.
        // @TODO don't load _everything_. Instead only load what's needed on demand
        \Yii::$container->get('craftQLFieldService')->load();
        $this->volumes->load();
        $this->categoryGroups->load();
        $this->tagGroups->load();
        $this->entryTypes->load();
        $this->sections->load();
        $this->globals->load();

        foreach ($this->entryTypes->all() as $entryType) {
            $name = StringHelper::graphQLNameForEntryType($entryType);
            $registry->add($name, Entry::class, $entryType);
        }

        foreach ($this->categoryGroups->all() as $categoryGroup) {
            $name = ucfirst($categoryGroup->handle).'Category';
            $registry->add($name, Category::class, $categoryGroup);
        }

        foreach ($this->tagGroups->all() as $tagGroup) {
            $name = ucfirst($tagGroup->handle).'Tags';
            $registry->add($name, Tag::class, $tagGroup);
        }

        foreach ($this->globals->all() as $set) {
            $name = ucfirst($set->handle);
            $registry->add($name, Globals::class, $set);
        }

        $schemaConfig = [];

        // $query = new Query($request);
        $query = (new InferredSchema($request))->parse(Query::class);

        // @TODO need to bring this back but in a different place, probably. Or not on the naked Query object, maybe on the inferred schema?
        // $event = new AlterQuerySchema;
        // $event->query = $query;
        // $query->trigger(AlterQuerySchema::EVENT, $event);

        $schemaConfig['query'] = $query->getRawGraphQLObject();

        $schemaConfig['typeLoader'] = function ($name) use ($registry) {
            return $registry->get($name);
        };

        $schemaConfig['directives'] = [
            \markhuot\CraftQL\Directives\Date::directive(),
        ];

        // $mutation = (new \markhuot\CraftQL\Types\Mutation($request))->getRawGraphQLObject();
        // $schemaConfig['mutation'] = $mutation;

        // @TODO need to do this if we're printing the schema or we won't get a full schema
        // use a closure on the dynamic types. this ensures that the full list of types will
        // only be loaded when GraphQL determines they are needed such as an introspection query
        // $schemaConfig['types'] = $registry->getDynamicTypes();
        $schemaConfig['types'] = function () use ($registry) {
            return $registry->getDynamicTypes();
        };

        $schema = new Schema($schemaConfig);

        if (Craft::$app->config->general->devMode) {
            // $schema->assertValid();
        }

        // $schemaText = SchemaPrinter::doPrint($schema);
        // header('content-type: text/plain');
        // echo $schemaText;
        // die;
        // $schemaAST = serialize(AST::toArray(Parser::parse($schemaText)));
        // Craft::$app->cache->set('foo', $schemaAST);

        return [$request, $schema];
    }

    function execute($request, $schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        $debug = null;
        // @TODO pass an empty array as validators to speed up execution even more
        return GraphQL::executeQuery($schema, $input, new Query($request), null, $variables, '', function ($source, $args, $context, $info) use ($request) {
            $fieldName = $info->fieldName;

            $property = null;

            if (is_object($source)) {
                if (method_exists($source, $method='getCraftQL'.ucfirst($fieldName))) {
                    if ($fieldName == 'entry') {
                        $newArgs = new EntryQueryArguments;
                        $newArgs->id = @$args['id'];
                        $args = $newArgs;
                    }
                    $property = $source->{$method}($request, $source, $args, $context, $info);
                }
                else if (method_exists($source, 'hasMethod') && $source->hasMethod($method='getCraftQL'.ucfirst($fieldName))) {
                    $property = $source->{$method}($request, $source, $args, $context, $info);
                }
                else if (method_exists($source, $method='get'.ucfirst($fieldName))) {
                    // we're not passing $source, $args, $context, $info here because
                    // we don't have confidence that the getField method is GraphQL
                    // aware and it could be expecting a completely different set of
                    // parameters.
                    $property = $source->{$method}();
                }
                else if (is_subclass_of($source, ProxyObject::class)) {
                    /** @var ProxyObject $source */
                    $property = $source->getProxiedValue($fieldName, $request, $source, $args, $context, $info);
                }
                else if (isset($source->{$fieldName})) {
                    $property = $source->{$fieldName};
                }
            }

            if ($property == null && (is_array($source) || $source instanceof \ArrayAccess)) {
                if (isset($source[$fieldName])) {
                    $property = $source[$fieldName];
                }
            }

            // proxy things
            $proxy = function ($prop) {
                if (is_a($prop, FieldData::class)) {
                    $prop = new RedactorFieldData($prop);
                }

                if (is_a($prop, Field::class)) {
                    $prop = new \markhuot\CraftQL\Types\Field($prop);
                }

                if (is_a($prop, EntryType::class)) {
                    $prop = new \markhuot\CraftQL\Types\EntryType($prop);
                }

                if (is_a($prop, User::class)) {
                    $prop = new \markhuot\CraftQL\Types\User($prop);
                }

                if (is_a($prop, \craft\elements\Entry::class)) {
                    $prop = new Entry($prop);
                }

                return $prop;
            };

            if (is_array($property)) {
                $property = array_map($proxy, $property);
            }
            else {
                $property = $proxy($property);
            }
            // end proxying

            return $property instanceof \Closure ? $property($source, $args, $context) : $property;
        })->toArray($debug);
    }

}
