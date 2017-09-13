<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use \newism\fields\models\GenderModel;

class NSMGenderBehavior extends DefaultBehavior
{
    static $genderObject;

    private static function object() {
        if (!empty(static::$genderObject)) {
            return static::$genderObject;
        }

        return static::$genderObject = new ObjectType([
            'name' => 'NSMGenderModel',
            'fields' => [
                'sex' => Type::string(),
                'identity' => Type::string(),
            ]
        ]);
    }

    public function getGraphQLDefaultFieldType($token, $field) {
        return static::object();
    }

    public function getGraphQLDefaultFieldResolver($token, $field, $root, $args) {
        return [
            'sex' => GenderModel::$sexLabels[$root->{$field->handle}->sex],
            'identity' => $root->{$field->handle}->identity,
        ];
    }

}