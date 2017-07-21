<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class SelectOne {

  static $enums = [];

  function getDefinition($field) {
    $options = [];
    foreach ($field['settings']['options'] as $option) {
      $options[$option['value']] = [
        'description' => $option['label'],
      ];
    }

    if (empty(static::$enums[$field->handle])) {
      static::$enums[$field->handle] = new EnumType([
        'name' => ucfirst($field->handle.'Enum'),
        'values' => $options,
      ]);
    }

    return [$field->handle => [
      'type' => static::$enums[$field->handle],
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        return (string)$root->{$field->handle} ?: null;
      }
    ]];
  }

}
