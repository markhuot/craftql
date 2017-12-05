<?php

namespace markhuot\CraftQL\GraphQLFields\Query\Connection;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\Base as BaseField;
use markhuot\CraftQL\Types\Entry;

class Edges extends BaseField {

    /**
     * A description of the field, exposed to the GraphQL api
     *
     * @var string
     */
    protected $description = 'The edges to this connection';

    /**
     * The resolve function for the field
     *
     * @param array $root
     * @param array $args
     * @param array $context
     * @param array $info
     * @return array
     */
    function getResolve($root, $args, $context, $info) {
        return array_map(function ($category) {
            return [
                'cursor' => '',
                'node' => $category
            ];
        }, $root['edges']);
    }

}