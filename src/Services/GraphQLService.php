<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Egulias\EmailValidator\Exception\CRLFAtTheEnd;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Type\Schema;
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

        $schemaConfig['directives'] = array_merge(GraphQL::getStandardDirectives(), [
            \markhuot\CraftQL\Directives\Date::directive(),
        ]);

        $mutation = (new \markhuot\CraftQL\Types\Mutation($request))->getRawGraphQLObject();
        $schemaConfig['mutation'] = $mutation;

        $schema = new Schema($schemaConfig);

        if (Craft::$app->config->general->devMode) {
            $schema->assertValid();
        }

        return $schema;
    }

    function execute($schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        return GraphQL::executeQuery($schema, $input, null, null, $variables)->toArray($debug);
    }

}
