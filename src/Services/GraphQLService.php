<?php

namespace markhuot\CraftQL\Services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Type\Schema;
use markhuot\CraftQL\Builders\Schema as Builder;
use markhuot\CraftQL\Schema\RelatedToGlobal;
use markhuot\CraftQL\Plugin;
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
    }

    function getSchema($token) {
        $request = new \markhuot\CraftQL\Request($token);
        $request->addCategoryGroups(new \markhuot\CraftQL\Factories\CategoryGroup($this->categoryGroups, $request));
        $request->addEntryTypes(new \markhuot\CraftQL\Factories\EntryType($this->entryTypes, $request));
        $request->addVolumes(new \markhuot\CraftQL\Factories\Volume($this->volumes, $request));
        $request->addSections(new \markhuot\CraftQL\Factories\Section($this->sections, $request));
        $request->addTagGroups(new \markhuot\CraftQL\Factories\TagGroup($this->tagGroups, $request));
        $request->addGlobals(new \markhuot\CraftQL\Factories\Globals($this->globals, $request));

        $schema = [];
        $schema['query'] = new \markhuot\CraftQL\Types\Query($request);
        $schema['types'] = array_merge(
            // $request->sections()->all(),

            array_map(function ($volume) {
                return $volume->getGraphQLObject();
            }, $request->volumes()->all()),

            array_map(function ($categoryGroup) {
                return $categoryGroup->getGraphQLObject();
            }, $request->categoryGroups()->all()),

            array_map(function ($tagGroup) {
                return $tagGroup->getGraphQLObject();
            }, $request->tagGroups()->all()),

            array_map(function ($entryType) {
                return $entryType->getGraphQLObject();
            }, $request->entryTypes()->all())
        );

        // $schema['directives'] = [
        //     \markhuot\CraftQL\Directives\Date::directive(),
        // ];

        // $mutation = new \markhuot\CraftQL\Types\Mutation($request);
        // if (count($mutation->getFields()) > 0) {
        //     $schema['mutation'] = $mutation;
        // }

        // var_dump(Schema::assertValid($schema));
        // die;

        // try {
            $schema = new Schema($schema);
            $schema->assertValid();
        // } catch(\yii\base\ErrorException $e) {
        //     echo $e->getMessage();
        //     die('foo');
        // } catch (\GraphQL\Error\InvariantViolation $e) {
        //     echo $e->getMessage();
        //     die;
        // }

        return $schema;
    }

    function execute($schema, $input, $variables = []) {
        $debug = Craft::$app->config->getGeneral()->devMode ? Debug::INCLUDE_DEBUG_MESSAGE | Debug::RETHROW_INTERNAL_EXCEPTIONS : null;
        return GraphQL::executeQuery($schema, $input, null, null, $variables)->toArray($debug);
    }

}
