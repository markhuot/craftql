<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;
use \newism\fields\models\GenderModel;

class NSMGenderBehavior extends Behavior
{
    static $type;
    static $genderObject;

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::string()]
        ];
    }

    public function upsert($value) {
        return $value;
    }

    private static function object() {
        if (!empty(static::$genderObject)) {
            return static::$genderObject;
        }

        return static::$genderObject = new ObjectType([
            'name' => 'NSMGender',
            'fields' => [
                'sex' => Type::string(),
                'identity' => Type::string(),
            ]
        ]);
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => static::object(),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return [
                        'sex' => GenderModel::$sexLabels[$root->{$field->handle}->sex],
                        'identity' => $root->{$field->handle}->identity,
                    ];
                },
            ],
        ];
    }

}