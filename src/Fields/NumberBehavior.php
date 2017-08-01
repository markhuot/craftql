<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class NumberBehavior extends Behavior
{
    
    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => $field->decimals == 0 ? Type::int() : Type::float()]
        ];
    }

    public function getGraphQLQueryFields() {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => $field->decimals == 0 ? Type::int() : Type::float(),
                'description' => $field->instructions,
            ],
        ];
    }

    public function upsert($value) {
        return $value;
    }

}