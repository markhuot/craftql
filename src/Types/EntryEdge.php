<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryEdge extends ObjectType {

    static function type($request) {
        return new static([
            'name' => 'EntryEdge',
            'fields' => [
                'cursor' => Type::string(),
                'node' => ['type' => \markhuot\CraftQL\Types\Entry::interface($request), 'resolve' => function ($root, $args, $context, $info) {
                    return $root['node'];
                }],
            ],
        ]);
    }

}