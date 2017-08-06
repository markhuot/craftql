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
    private $mutationType;
    private $volumes;
    private $categoryGroups;

    function __construct(
        \markhuot\CraftQL\Types\Mutation $mutationType,
        \markhuot\CraftQL\Repositories\Volumes $volumes,
        \markhuot\CraftQL\Repositories\CategoryGroup $categoryGroups
    ) {
        $this->mutationType = $mutationType;
        $this->volumes = $volumes;
        $this->categoryGroups = $categoryGroups;
    }

    /**
     * Bootstrap the schema
     *
     * @return void
     */
    function bootstrap($token) {
        \markhuot\CraftQL\Types\Entry::bootstrap();
        \markhuot\CraftQL\Types\EntryType::bootstrap();

        $schema = [];
        $schema['query'] = new \markhuot\CraftQL\Types\Query($token);
        
        $this->volumes->loadAllVolumes();
        $this->categoryGroups->loadAllGroups();
        $schema['types'] = array_merge(
            $this->volumes->getAllVolumes(),
            $this->categoryGroups->getAllGroups(),
            \markhuot\CraftQL\Types\EntryType::some($token->queryableEntryTypeIds())
        );

        $mutation = $this->mutationType->getType($token);
        if (count($mutation->getFields()) > 0) {
            $schema['mutation'] = $mutation;
        }

        $this->schema = new Schema($schema);
    }

    function execute($input, $variables = []) {
        return GraphQL::execute($this->schema, $input, null, null, $variables);
    }

}
