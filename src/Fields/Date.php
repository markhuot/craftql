<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Date {

  function getDefinition($field) {
    return [
      "{$field->handle}Timestamp" => [
        'type' => Type::int(),
        'description' => $field->instructions,
        'resolve' => function ($root, $args) use ($field) {
          return $root->{$field->handle} ? $root->{$field->handle}->format('U') : null;
        }
      ],
      $field->handle => [
        'type' => Type::string(),
        'description' => $field->instructions,
        'args' => [
          ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r'],
        ],
        'resolve' => function ($root, $args) use ($field) {
          return $root->{$field->handle} ? $root->{$field->handle}->format($args['format']) : null;
        }
      ],
    ];
  }

  function getGraphQlType($field) {
    return Type::string();
  }

}
