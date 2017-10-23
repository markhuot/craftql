<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryConnection extends ObjectType {

    static $type;

    static function listsType($request) {
        return EntryEdge::type($request);
    }

    static function type($request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        $reflect = new \ReflectionClass(static::class);

        return static::$type = new static([
            'name' => $reflect->getShortName(),
            'fields' => [
                'totalCount' => Type::nonNull(Type::int()),
                'pageInfo' => PageInfo::type($request),
                'edges' => ['type' => Type::listOf(static::listsType($request)), 'resolve' => function ($root, $args) {
                    return array_map(function ($entry) {
                        return [
                            'cursor' => '',
                            'node' => $entry
                        ];
                    }, $root['edges']);
                }],
                'entries' => ['type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)), 'resolve' => function ($root, $args) {
                    return $root['edges'];
                }],
            ],
        ]);
    }

}