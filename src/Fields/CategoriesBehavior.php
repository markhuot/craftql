<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use yii\base\Behavior;

class CategoriesBehavior extends Behavior
{
    static $enums = [];

    function getEnumFor($field) {
        if (isset(static::$enums[$field->handle])) {
            return static::$enums[$field->handle];
        }

        $options = [];
        foreach ($field['settings']['options'] as $option) {
            $options[$option['value']] = [
                'description' => $option['label'],
            ];
        }

        return static::$enums[$field->handle] = new EnumType([
            'name' => ucfirst($field->handle.'Enum'),
            'values' => $options,
        ]);
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        if (!$request->token()->can('query:categories')) {
            return [];
        }

        if (preg_match('/^group:(\d+)$/', $field->source, $matches)) {
            $groupId = $matches[1];

            return [
                $field->handle => [
                    'type' => Type::listOf($request->categoryGroup($groupId)),
                    'description' => $field->instructions,
                    'resolve' => function ($root, $args) use ($field) {
                        return $root->{$field->handle}->all();
                    }
                ]
            ];
        }

        return [];
    }

    public function upsert($value) {
        return $value;
    }

}