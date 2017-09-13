<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class NSMTelephoneBehavior extends Behavior
{
    static $type;

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::string()]
        ];
    }

    public function upsert($value) {
        return $value;
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::string(),
                'args' => [
                    'format' => Type::string(),
                ],
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    if (!empty($args['format'])) {
                        return $root->{$field->handle}->format($args['format']);
                    }
                    
                    return (string)$root->{$field->handle};
                },
            ],
        ];
    }

}