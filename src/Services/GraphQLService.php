<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Language\Parser;
use GraphQL\Type\Schema;
use GraphQL\Utils\AST;
use GraphQL\Utils\BuildSchema;
use GraphQL\Utils\SchemaPrinter;
use GraphQL\Validator\DocumentValidator;
use GraphQL\Validator\Rules\QueryComplexity;
use GraphQL\Validator\Rules\QueryDepth;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Events\AlterQuerySchema;
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
        $this->volumes->load();
        $this->categoryGroups->load();
        $this->tagGroups->load();
        $this->entryTypes->load();
        $this->sections->load();
        $this->globals->load();

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $fieldService->load();

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
//        xdebug_start_trace('/Users/markhuot/Desktop/getSchema');
        $cacheKey = 'craftQlSchema'.$token->uid();
//        Craft::$app->cache->delete($cacheKey);

        if ($cacheValue = \Craft::$app->cache->get($cacheKey)) {
            $doc = Parser::parse($cacheValue);
//            var_dump($doc);
//            die;
//            var_dump(BuildSchema::buildAST($doc));
//            die;
            $schema = BuildSchema::build($doc);
//            var_dump($schema);
//            die;
//            header('content-type: text/plain');
//            echo($cacheValue);
//            die;
            return $schema;
        }

        $request = new \markhuot\CraftQL\Request($token);
        $request->addCategoryGroups(new \markhuot\CraftQL\Factories\CategoryGroup($this->categoryGroups, $request));
        $request->addEntryTypes(new \markhuot\CraftQL\Factories\EntryType($this->entryTypes, $request));
        $request->addVolumes(new \markhuot\CraftQL\Factories\Volume($this->volumes, $request));
        $request->addSections(new \markhuot\CraftQL\Factories\Section($this->sections, $request));
        $request->addTagGroups(new \markhuot\CraftQL\Factories\TagGroup($this->tagGroups, $request));
        $request->addGlobals(new \markhuot\CraftQL\Factories\Globals($this->globals, $request));

        $schemaConfig = [];

        $query = new \markhuot\CraftQL\Types\Query($request);

        $event = new AlterQuerySchema;
        $event->query = $query;
        $query->trigger(AlterQuerySchema::EVENT, $event);

        $schemaConfig['query'] = $query->getRawGraphQLObject();
        $schemaConfig['types'] = function () use ($request, $query) {
            return array_merge(
                array_map(function ($section) {
                    return $section->getRawGraphQLObject();
                }, $request->sections()->all()),

                array_map(function ($volume) {
                    return $volume->getRawGraphQLObject();
                }, $request->volumes()->all()),

                array_map(function ($categoryGroup) {
                    return $categoryGroup->getRawGraphQLObject();
                }, $request->categoryGroups()->all()),

                array_map(function ($tagGroup) {
                    return $tagGroup->getRawGraphQLObject();
                }, $request->tagGroups()->all()),

                array_map(function ($entryType) {
                    return $entryType->getRawGraphQLObject();
                }, $request->entryTypes()->all()),

                [\markhuot\CraftQL\Directives\Date::dateFormatTypesEnum()],

                $query->getConcreteTypes()
            );
        };

        $schemaConfig['directives'] = [
            \markhuot\CraftQL\Directives\Date::directive(),
        ];

        $mutation = (new \markhuot\CraftQL\Types\Mutation($request))->getRawGraphQLObject();
        $schemaConfig['mutation'] = $mutation;

        $request->fooBar();
//        \Yii::beginProfile('newSchema', 'newSchema');
//        xdebug_start_trace('/Users/markhuot/Desktop/newSchemaShorter');
        $schema = new Schema($schemaConfig);
//        xdebug_stop_trace();
//        \Yii::endProfile('newSchema', 'newSchema');
//        var_dump($schema);
//        serialize($schema);
//        die;
//        var_dump($schema->getAstNode());
//        die;
//        var_dump(AST::toArray($schema));
//        die;

//        var_dump(serialize($schema));
//        header('content-type: text/plain');
//        echo SchemaPrinter::doPrint($schema);
//        die;

//        \Craft::$app->cache->add($cacheKey, SchemaPrinter::doPrint($schema));
//        var_dump($schema);
//        die;

        if (Craft::$app->config->general->devMode) {
            $schema->assertValid();
        }

//        xdebug_stop_trace();
        return $schema;
    }

    function execute($schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        return GraphQL::executeQuery($schema, $input, null, null, $variables)->toArray($debug);
    }

}
