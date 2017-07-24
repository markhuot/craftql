<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class PositionSelect {

    static $enum;

    function getEnum($field) {
        if (static::$enum) {
            return static::$enum;
        }
        
        return static::$enum = new EnumType([
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

    function getDefinition($field) {
        return [
            $field->handle => [
                'type' => $this->getEnum($field),
                'description' => $field->instructions,
            ],
        ];
    }

  function getArg($field) {
    return [
        $field->handle => ['type' => $this->getEnum($field)]
    ];
  }

}
