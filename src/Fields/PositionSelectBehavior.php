<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class PositionSelectBehavior extends Behavior
{
    static $enums;

    function getEnum($field) {
        if (!empty(static::$enums[$field->handle])) {
            return static::$enums[$field->handle];
        }

        $values = [];
        if (in_array('left', $field->options)) { $values['left'] = 'Left'; }
        if (in_array('center', $field->options)) { $values['center'] = 'Center'; }
        if (in_array('right', $field->options)) { $values['right'] = 'Right'; }
        if (in_array('full', $field->options)) { $values['full'] = 'Full'; }
        if (in_array('drop-left', $field->options)) { $values['dropLeft'] = 'Drop Left'; }
        if (in_array('drop-right', $field->options)) { $values['dropRight'] = 'Drop Right'; }

        return static::$enums[$field->handle] = new EnumType([
            'name' => ucfirst($field->handle).'Enum',
            'values' => $values,
        ]);
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => $this->getEnum($field)]
        ];
    }

    public function getGraphQLQueryFields($token) {
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