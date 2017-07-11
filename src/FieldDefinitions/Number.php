<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Number {

  function getDefinition($field) {
    return [
      $field->handle => [
        'type' => $field->decimals == 0 ? Type::int() : Type::float(),
      ],
    ];
  }

}
