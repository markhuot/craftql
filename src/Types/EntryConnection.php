<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryConnection extends ObjectType {

    static $type;

    static function type($request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new static([
            'name' => 'EntryConnection',
            'fields' => [
                'totalCount' => Type::nonNull(Type::int()),
                'pageInfo' => PageInfo::type($request),
                'edges' => ['type' => Type::listOf(EntryEdge::type($request)), 'resolve' => function ($root, $args) {
                    return array_map(function ($entry) {
                        return [
                            'cursor' => '',
                            'node' => $entry
                        ];
                    }, $root['edges']);
                }],
            ],
        ]);
    }

}