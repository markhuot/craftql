<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\GraphQLFields\Query\Connection\Edges as EdgesField;

class EntryConnection extends ObjectType {

    static function edgesType($request) {
        return EntryEdge::singleton($request);
    }

    protected function fields(Request $request) {
        return [
            'totalCount' => Type::nonNull(Type::int()),
            'pageInfo' => PageInfo::type($request),
            'edges' => (new EdgesField($request))
                ->setType(Type::listOf(static::edgesType($request)))
                ->toArray(),
            'entries' => ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)), 'resolve' => function ($root, $args) {
                return $root['edges'];
            }],
        ];
    }

}