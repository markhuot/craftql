<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Schema;
use GraphQL\Utils\SchemaPrinter;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Events\AlterQuerySchema;
use markhuot\CraftQL\Types\EntryInterface;
use markhuot\CraftQL\Types\Query;
use yii\base\Component;
use Yii;

class GraphQLService extends Component {

    private $schema;
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
        $request->addCategoryGroups(new \markhuot\CraftQL\Factories\CategoryGroup($this->categoryGroups, $request));
        $request->addEntryTypes(new \markhuot\CraftQL\Factories\EntryType($this->entryTypes, $request));
        $request->addVolumes(new \markhuot\CraftQL\Factories\Volume($this->volumes, $request));
        $request->addSections(new \markhuot\CraftQL\Factories\Section($this->sections, $request));
        $request->addTagGroups(new \markhuot\CraftQL\Factories\TagGroup($this->tagGroups, $request));
        $request->addGlobals(new \markhuot\CraftQL\Factories\Globals($this->globals, $request));

        $schemaConfig = [];

        $query = new \markhuot\CraftQL\Types\Query($request);

        // $event = new AlterQuerySchema;
        // $event->query = $query;
        // $query->trigger(AlterQuerySchema::EVENT, $event);

        $schemaObj = new \markhuot\CraftQL\Builder2\Schema;
        $schemaConfig['query'] = $schemaObj->getType(Query::class);

        $schemaObj->addConcreteType(EntryInterface::class, [
            'name' => 'Blog',
            'description' => 'foo',
        ]);

        $schemaConfig['types'] = $schemaObj->getTypes();

        // $schemaConfig['query'] = $query->getGraphQlConfig();
//        $schemaConfig['types'] = function () use ($request, $query) {
//            return array_merge(
//                array_map(function ($section) {
//                    return $section->getRawGraphQLObject();
//                }, $request->sections()->all()),
//
//                array_map(function ($volume) {
//                    return $volume->getRawGraphQLObject();
//                }, $request->volumes()->all()),
//
//                array_map(function ($categoryGroup) {
//                    return $categoryGroup->getRawGraphQLObject();
//                }, $request->categoryGroups()->all()),
//
//                array_map(function ($tagGroup) {
//                    return $tagGroup->getRawGraphQLObject();
//                }, $request->tagGroups()->all()),
//
//                array_map(function ($entryType) {
//                    return $entryType->getRawGraphQLObject();
//                }, $request->entryTypes()->all()),
//
//                [\markhuot\CraftQL\Directives\Date::dateFormatTypesEnum()],
//
//                $query->getConcreteTypes()
//            );
//        };

//        $schemaConfig['directives'] = [
//            \markhuot\CraftQL\Directives\Date::directive(),
//        ];
//
//        $mutation = (new \markhuot\CraftQL\Types\Mutation($request))->getRawGraphQLObject();
//        $schemaConfig['mutation'] = $mutation;

        $schema = new Schema($schemaConfig);

       $print = SchemaPrinter::doPrint($schema);
       header('content-type: text/plain');
       echo $print;
       die;

        if (Craft::$app->config->general->devMode) {
            $schema->assertValid();
        }

        return $schema;
    }

    function execute($schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        return GraphQL::executeQuery($schema, $input, new Query, null, $variables, '', function ($source, $args, $context, ResolveInfo $info) {
            $fieldName = $info->fieldName;

            if (!empty($info->parentType->description)) {
                // $factory = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                // $description = $info->parentType->description;
                // $docs = $factory->create($description);
                // $resolveTags = $docs->getTagsByName('resolve');
                // if (!empty($resolveTags)) {
                //     $className = '\\'.trim($resolveTags[0]->getDescription(), '/');
                //     $class = new $className;
                //     $methodName = 'resolve'.ucfirst($fieldName).'Field';
                //     if (method_exists($class, $methodName)) {
                //         return $class->$methodName($source, $args, $context, $info);
                //     }
                //     else if (property_exists($class, $fieldName)) {
                //         return $class->{$fieldName};
                //     }
                // }
            }

            $property = null;

            if (is_array($source) || $source instanceof \ArrayAccess) {
                if (isset($source[$fieldName])) {
                    $property = $source[$fieldName];
                }
            }
            else if (is_object($source)) {
                if (isset($source->{$fieldName})) {
                    $property = $source->{$fieldName};
                }
                else if (method_exists($source, $method='get'.ucfirst($fieldName))) {
                    $property = $source->{$method}($source, $args, $context, $info);
                }
            }

            // downcast things we're able to
            switch (get_class($info->returnType)) {
                case \GraphQL\Type\Definition\StringType::class: $property = (string)$property;
            }

            return $property instanceof \Closure ? $property($source, $args, $context) : $property;
        })->toArray($debug);
    }

}
