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
                'type' => static::type(),
                'description' => $field->instructions,
            ],
        ];
    }

    static function type() {
        if (static::$type) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'DoxterFieldData',
            'fields' => [
                'markdown' => ['type' => Type::string(), 'resolve' => function ($root, $args) {
                    return $root->getRaw();
                }],
                'html' => ['type' => Type::string(), 'resolve' => function ($root, $args) {
                    return $root->getHtml();
                }],
            ]
        ]);
    }

}