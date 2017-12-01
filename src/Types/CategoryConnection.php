<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\GraphQLFields\Query\Connection\Edges as EdgesField;

class CategoryConnection extends ObjectType {

    protected function fields(Request $request) {
        return [
            'totalCount' => Type::nonNull(Type::int()),
            'pageInfo' => PageInfo::type($request),
            'edges' => (new EdgesField($request))
                ->setType(Type::listOf(CategoryEdge::singleton($request)))
                ->toArray(),
            'categories' => [
                'type' => Type::listOf(Category::interface($request)),
                'resolve' => function ($root, $args) {
                    return $root['edges'];
                }
            ],
        ];
    }

}