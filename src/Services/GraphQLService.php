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
    private $queryType;

    function __construct(
        \markhuot\CraftQL\GraphQL\Types\Mutation $mutationType,
        \markhuot\CraftQL\GraphQL\Types\Query $queryType
    ) {
        $this->mutationType = $mutationType;
        $this->queryType = $queryType;
    }

    function bootstrap($writable=false) {
        $schema = [];
        $schema['query'] = $this->queryType->getType();

        $schema['types'] = [];
        $schema['types'] = array_merge($schema['types'], $this->queryType->getTypes());

        if ($writable) {
            $schema['mutation'] = $this->mutationType->getType();
        }

        $this->schema = new Schema($schema);
    }

    function execute($input, $variables = []) {
        return GraphQL::execute($this->schema, $input, null, null, $variables);
    }

}
