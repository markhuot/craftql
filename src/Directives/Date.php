<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\DirectiveLocation;
use GraphQL\Type\Definition\FieldArgument;

class Date {

    static $directive;

    static function directive() {
        if (static::$directive) {
            return static::$directive;
        }

        return static::$directive = new Directive([
            'name' => 'date',
            'description' => 'Transform Timestamp types into string representations',
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
