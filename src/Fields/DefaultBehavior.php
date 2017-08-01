<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class DefaultBehavior extends Behavior
{
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::string()]
        ];
    }

    public function getGraphQLQueryFields() {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::string(),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return $field->normalizeValue($root->{$field->handle});
                }
            ],
        ];
    }

    public function upsert($value) {
        return $value;
    }

}