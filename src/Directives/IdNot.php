<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\DirectiveLocation;
use GraphQL\Type\Definition\FieldArgument;

class IdNot {

    static $directive;
    static $dateFormatTypesEnum;

    static function directive() {
        if (static::$directive) {
            return static::$directive;
        }

        return static::$directive = new Directive([
            'name' => 'idNot',
            'description' => 'Mask records in an array of ids, against appearing for a field',
            'locations' => [
                DirectiveLocation::FIELD,
                DirectiveLocation::FRAGMENT_SPREAD,
                DirectiveLocation::INLINE_FRAGMENT,
            ],
            'args' => [
                new FieldArgument([
                    'name' => 'field',
                    'type' => Type::nonNull(Type::string()),
                    'description' => 'string name of field to mask',
                    'defaultValue' => 0
                ]),
                new FieldArgument([
                    'name' => 'in',
                    'type' => Type::nonNull(Type::listOf(Type::int())),
                    'description' => 'array of ids',
                    'defaultValue' => 0
                ])
            ]
        ]);
    }
}
