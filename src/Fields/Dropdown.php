<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Dropdown {

  function getDefinition($field) {
    return [
      $field->handle => [
        'type' => Type::string(),
      ],
    ];
  }

}
