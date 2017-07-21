<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Number {

  function getDefinition($field) {
    return [
      $field->handle => [
        'type' => $field->decimals == 0 ? Type::int() : Type::float(),
        'description' => $field->instructions,
      ],
    ];
  }

  function getGraphQlType($field) {
    return Type::string();
  }

}
