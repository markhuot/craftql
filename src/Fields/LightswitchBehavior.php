<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class LightswitchBehavior extends Behavior
{
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;
        
        return [
            $field->handle => ['type' => Type::boolean()]
        ];
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::boolean(),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return $root->{$field->handle};
                }
            ],
        ];
    }

    public function upsert($value) {
        return $value;
    }

}