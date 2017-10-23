<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class PageInfo extends ObjectType {

    static $type;

    static function type($request) {
        if (static::$type) {
            return static::$type;
        }

        return static::$type = new static([
            'name' => 'PageInfo',
            'fields' => [
                'hasPreviousPage' => ['type' => Type::nonNull(Type::boolean()), 'resolve' => function ($root, $args) {
                    return $root->currentPage > 1;
                }],
                'hasNextPage' => ['type' => Type::nonNull(Type::boolean()), 'resolve' => function ($root, $args) {
                    return $root->currentPage < $root->totalPages;
                }],
                'currentPage' => Type::int(),
                'totalPages' => Type::int(),
                'first' => Type::int(),
                'last' => Type::int(),
            ],
        ]);
    }

}