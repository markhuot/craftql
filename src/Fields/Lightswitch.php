<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class Lightswitch {

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::boolean(),
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle};
      }
    ]];
  }

  function getArg($field) {
    return [
      $field->handle => ['type' => Type::boolean()]
    ];
  }

}
