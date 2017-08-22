<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class DoxterBehavior extends Behavior
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
                    'raw' => Type::boolean(),
                ],
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    if (!empty($args['raw'])) {
                        return $root->{$field->handle}->getRaw();
                    }
                    
                    return $root->{$field->handle}->getHtml();
                },
            ],
        ];
    }

}