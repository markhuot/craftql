<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldDateService extends BaseApplicationComponent {

  function getDefinition($field) {
    return [
      'type' => Type::int(),
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle}->format('U');
      }
    ];
  }

}
