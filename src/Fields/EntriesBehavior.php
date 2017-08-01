<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use yii\base\Behavior;

class EntriesBehavior extends Behavior
{
    public function getGraphQLMutationArgs() {
        $field = $this->owner;
        
        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getGraphQLQueryFields() {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface()),
                'description' => $field->instructions,
                'args' => \markhuot\CraftQL\Types\Entry::args(),
                'resolve' => function ($root, $args) use ($field) {
                    $criteria = $root->{$field->handle};
                    foreach ($args as $key => $value) {
                        $criteria->{$key} = $value;
                    }
                    return $criteria;
                }
            ]
        ];
    }

    public function upsert($values) {
        return $values;
    }

}