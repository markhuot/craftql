<?php

namespace markhuot\CraftQL\Services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL;
use GraphQL\Schema;
use Underscore\Types\Arrays;
use markhuot\CraftQL\Plugin;
use yii\base\Component;
use Yii;

class GraphQLService extends Component {

    private $schema;
    private $volumes;
    private $categoryGroups;
    private $entryTypes;

    function __construct(
        \markhuot\CraftQL\Repositories\Volumes $volumes,
        \markhuot\CraftQL\Repositories\CategoryGroup $categoryGroups,
        \markhuot\CraftQL\Repositories\EntryType $entryTypes
    ) {
        $this->volumes = $volumes;
        $this->categoryGroups = $categoryGroups;
        $this->entryTypes = $entryTypes;
    }

    /**
     * Bootstrap the schema
     *
     * @return void
     */
    function bootstrap() {
        $this->volumes->load();
        $this->categoryGroups->load();
        $this->entryTypes->load();
    }

    function getSchema($token) {
        $request = new \markhuot\CraftQL\Request($token);
        $request->addCategoryGroups(new \markhuot\CraftQL\Factories\CategoryGroup($this->categoryGroups, $request));
        $request->addEntryTypes(new \markhuot\CraftQL\Factories\EntryType($this->entryTypes, $request));
        $request->addVolumes(new \markhuot\CraftQL\Factories\Volume($this->volumes, $request));

        $schema = [];
        $schema['query'] = new \markhuot\CraftQL\Types\Query($request);
        $schema['types'] = array_merge(
            $request->volumes()->all(),
            $request->entryTypes()->all()
        );

        // var_dump($schema['query']);
        // die;

        // $mutation = new \markhuot\CraftQL\Types\Mutation($token);
        // if (count($mutation->getFields()) > 0) {
        //     // $schema['mutation'] = $mutation;
        // }

        return new Schema($schema);
    }

    function execute($schema, $input, $variables = []) {
        return GraphQL::execute($schema, $input, null, null, $variables);
    }

}
