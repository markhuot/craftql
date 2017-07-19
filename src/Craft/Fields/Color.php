<?php

namespace markhuot\CraftQL\Craft\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class Color {

  function getDefinition($field) {
    return [
      $field->handle => [
        'type' => Type::string(),
      ],
    ];
  }

}
