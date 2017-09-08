<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class RichTextBehavior extends Behavior
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
                'type' => Type::string(),
                'description' => $field->instructions,
                'args' => [
                    ['name' => 'page', 'type' => Type::int()],
                ],
                'resolve' => function ($root, $args) use ($field) {
                    if (!empty($args['page'])) {
                        return (string)$root->{$field->handle}->getPage($args['page']);
                    }

                    return (string)$root->{$field->handle};
                }
            ],
        ];
    }

    public function upsert($value) {
        return $value;
    }

}