<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use yii\base\Behavior;

class SelectMultipleBehavior extends SelectOneBehavior
{

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::listOf($this->getEnumFor($field)),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    $values = [];
                    foreach ($root->{$field->handle} as $option) {
                        $values[] = $option->value;
                    }
                    return $values;
                }
            ]
        ];
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf($this->getEnumFor($field))]
        ];
    }

}