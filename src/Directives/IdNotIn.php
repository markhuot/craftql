<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\DirectiveLocation;
use GraphQL\Type\Definition\FieldArgument;

class IdNotIn {

    static $directive;

    static function directive() {
        if (static::$directive) {
            return static::$directive;
        }

        return static::$directive = new Directive([
            'name' => 'idNotIn',
            'description' => 'Mask listed ids from appearing for fields',
            'locations' => [
                DirectiveLocation::FIELD,
            ],
            'args' => [
                new FieldArgument([
                    'name' => 'as',
                    'type' => Type::string(),
                    'description' => 'Date formatting',
                    'defaultValue' => 'r'
                ])
            ]
        ]);
    }

}
