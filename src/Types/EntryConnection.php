<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;

class EntryConnection extends ObjectType {

    static $type;

    static function edgesType($request) {
        return EntryEdge::make($request);
    }

    static function make(Request $request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        $reflect = new \ReflectionClass(static::class);

        return static::$type = new static([
            'name' => $reflect->getShortName(),
            'fields' => [
                'totalCount' => Type::nonNull(Type::int()),
                'pageInfo' => PageInfo::type($request),
                'edges' => ['type' => Type::listOf(static::edgesType($request)), 'resolve' => function ($root, $args) {
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