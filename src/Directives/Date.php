<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Language\DirectiveLocation;
use GraphQL\Type\Definition\FieldArgument;
use markhuot\CraftQL\Types\DateFormatTypes;

class Date {

    static $directive;
    static $dateFormatTypesEnum;

    static function directive() {
        if (static::$directive) {
            return static::$directive;
        }

        return static::$directive = new Directive([
            'name' => 'date',
            'description' => 'Transform Timestamp types into string representations',
            'locations' => [
                DirectiveLocation::FIELD_DEFINITION,
            ],
            'args' => [
                new FieldArgument([
                    'name' => 'as',
                    'type' => Type::string(),
                    'description' => 'Date formatting',
                    'defaultValue' => 'r'
                ]),
                new FieldArgument([
                    'name' => 'timezone',
                    'type' => Type::string(),
                    'description' => 'The full name of the timezone, defaults to GMT. (E.g., America/New_York)',
                    'defaultValue' => 'GMT'
                ]),
                new FieldArgument([
                    'name' => 'format',
                    'type' => DateFormatTypes::class,
                    'description' => 'A standard format to use, overrides the `as` argument',
                ]),
                new FieldArgument([
                    'name' => 'locale',
                    'type' => Type::string(),
                    'description' => 'The locale to use when formatting the date',
                ])
            ]
        ]);
    }

}
