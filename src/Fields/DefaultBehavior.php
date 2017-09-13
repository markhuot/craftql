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

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => $this->getGraphQLDefaultFieldType($token, $field),
                'args' => $this->getGraphQLDefaultFieldArgs($token, $field),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($token, $field) {
                    return $this->getGraphQLDefaultFieldResolver($token, $field, $root, $args);
                }
            ],
        ];
    }

    public function getGraphQLDefaultFieldType($token, $field) {
        return Type::string();
    }

    public function getGraphQLDefaultFieldArgs($token, $field) {
        return [];
    }

    public function getGraphQLDefaultFieldResolver($token, $field, $root, $args) {
        return (string)$root->{$field->handle};
    }

    public function upsert($value) {
        return $value;
    }

}