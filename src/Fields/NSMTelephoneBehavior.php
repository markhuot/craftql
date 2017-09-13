<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class NSMTelephoneBehavior extends DefaultBehavior
{

    public function getGraphQLDefaultFieldArgs($token, $field) {
        return [
            'format' => Type::string(),
        ];
    }

    public function getGraphQLDefaultFieldResolver($token, $field, $root, $args) {
        if (!empty($args['format'])) {
            return $root->{$field->handle}->format($args['format']);
        }

        return (string)$root->{$field->handle};
    }

}