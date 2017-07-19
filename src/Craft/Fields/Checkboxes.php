<?php

namespace markhuot\CraftQL\Craft\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class Checkboxes {

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
      'type' => Type::listOf(static::$enums[$field->handle]),
      'resolve' => function ($root, $args) use ($field) {
        $values = [];
        foreach ($root->{$field->handle} as $option) {
          $values[] = $option->value;
        }
        return $values;
      }
    ]];
  }

}
