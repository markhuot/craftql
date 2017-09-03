<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class TagsBehavior extends Behavior
{
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        $source = $field->settings['source'];
        if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
            $groupId = $matches[1];

            return [
                $field->handle => [
                    'type' => Type::listOf($request->tagGroup($groupId)),
                    'description' => $field->instructions,
                    'resolve' => function ($root, $args) use ($field) {
                        return $root->{$field->handle}->all();
                    }
                ]
            ];
        }
    }

    public function upsert($values) {
        return $values;
    }

}