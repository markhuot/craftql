<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class PageInfo extends ObjectType {

    static function type($request) {
        return new static([
            'name' => 'PageInfo',
            'fields' => [
                'hasPreviousPage' => ['type' => Type::nonNull(Type::boolean()), 'resolve' => function ($root, $args) {
                    return $root->currentPage > 1;
                }],
                'hasNextPage' => ['type' => Type::nonNull(Type::boolean()), 'resolve' => function ($root, $args) {
                    return $root->currentPage < $root->totalPages;
                }],
            ],
        ]);
    }

}