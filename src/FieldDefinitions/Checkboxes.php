<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class Checkboxes {

  function getDefinition($field) {
    $options = [];
    foreach ($field['settings']['options'] as $option) {
      $options[$option['value']] = [
        'description' => $option['label'],
      ];
    }

    $enumType = new EnumType([
      'name' => ucfirst($field->handle.'Enum'),
      'values' => $options,
    ]);

    return [$field->handle => [
      'type' => Type::listOf($enumType),
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
