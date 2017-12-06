<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use yii\base\Behavior;

class SelectOneBehavior extends Behavior
{
    static $enums = [];

    function getEnum($field) {
        if (isset(static::$enums[$field->handle])) {
            return static::$enums[$field->handle];
        }

        $options = [];
        foreach ($field['settings']['options'] as $option) {
            $value = $option['value'];
            $value = preg_replace('/[^a-z0-9]+/i', ' ', $value);
            $value = \craft\helpers\StringHelper::toCamelCase($value);
            if ($value === '') {
                $value = 'empty';
            }
            $options[$value] = [
                'description' => $option['label'] ?: 'empty',
            ];
        }

        return static::$enums[$field->handle] = new EnumType([
            'name' => ucfirst($field->handle.'Enum'),
            'values' => $options,
        ]);
    }

    function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => $this->getEnum($field),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return (string)$root->{$field->handle} ?: null;
                }
            ]
        ];
    }

    function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => $this->getEnum($field)]
        ];
    }

    public function upsert($value) {
        return $value;
    }
}
