<?php

namespace markhuot\CraftQL\Directives;

use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\Directive;
use GraphQL\Type\Definition\DirectiveLocation;
use GraphQL\Type\Definition\FieldArgument;

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
                DirectiveLocation::FIELD,
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
                    'type' => static::dateFormatTypesEnum(),
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

    static function dateFormatTypesEnum() {
        if (!empty(static::$dateFormatTypesEnum)) {
            return static::$dateFormatTypesEnum;
        }

        return static::$dateFormatTypesEnum = new EnumType([
            'name' => 'DateFormatTypes',
            'values' => [
                'atom' => ['description' => 'Atom feeds'],
                'cookie' => ['description' => 'HTTP cookies'],
                'iso8601' => ['description' => 'ISO-8601 spec'],
                'rfc822' => ['description' => 'RFC-822 spec'],
                'rfc850' => ['description' => 'RFC-850 spec'],
                'rfc1036' => ['description' => 'RFC-1036 spec'],
                'rfc1123' => ['description' => 'RFC-1123 spec'],
                'rfc2822' => ['description' => 'RFC-2822 spec'],
                'rfc3339' => ['description' => 'RFC-3339 spec'],
                'rss' => ['description' => 'RSS feed'],
                'w3c' => ['description' => 'W3C spec'],
            ]
        ]);
    }

}
