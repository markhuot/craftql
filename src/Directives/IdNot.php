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
            'description' => 'Mask records in array of ids against appearing for fields',
            'locations' => [
                DirectiveLocation::FIELD,
                DirectiveLocation::FRAGMENT_SPREAD,
                DirectiveLocation::INLINE_FRAGMENT,
            ],
            'args' => [
                new FieldArgument([
                    'name' => 'in',
                    'type' => Type::nonNull(Type::id()),
                    'description' => 'array of ids',
                    'defaultValue' => 0
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
