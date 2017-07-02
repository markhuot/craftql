<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldDateService extends BaseApplicationComponent {

  function getDefinition($field) {
    return [
      "{$field->handle}Timestamp" => [
        'type' => Type::int(),
        'resolve' => function ($root, $args) use ($field) {
          return $root->{$field->handle} ? $root->{$field->handle}->format('U') : null;
        }
      ],
      $field->handle => [
        'type' => Type::string(),
        'args' => [
          ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r'],
        ],
        'resolve' => function ($root, $args) use ($field) {
          return $root->{$field->handle} ? $root->{$field->handle}->format($args['format']) : null;
        }
      ],
    ];
  }

}
