<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class PositionSelectBehavior extends Behavior
{
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

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => $this->getEnum($field)]
        ];
    }

    public function getGraphQLQueryFields() {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => $this->getEnum($field),
                'description' => $field->instructions,
            ],
        ];
    }

    public function upsert($value) {
        return $value;
    }

}