<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Unknown {

    function getDefinition($field) {
        return [
            $field->handle => [
                'type' => Type::string(),
                'description' => $field->instructions,
                'resolve' => function ($root, $args) use ($field) {
                    return $field->normalizeValue($root->{$field->handle});
                }
            ],
        ];
    }

    function getGraphQlType($field) {
        return Type::string();
    }

}
