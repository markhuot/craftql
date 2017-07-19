<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class PositionSelect {

    static $enum;

    function getDefinition($field) {
        if (!static::$enum) {
            static::$enum = new EnumType([
                'name' => ucfirst($field->handle).'Enum',
                'values' => [
                    'left' => 'left',
                    'center' => 'center',
                    'right' => 'right',
                    'full' => 'full',
                    'dropleft' => 'drop-left',
                    'dropright' => 'drop-right',
                ],
            ]);
        }

        return [
            $field->handle => [
                'type' => static::$enum,
            ],
        ];
    }

}
