<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class CraftQL_FieldCheckboxesService extends BaseApplicationComponent {

  function getDefinition($field) {
    $options = [];
    foreach ($field['settings']['options'] as $option) {
      $options[$option['value']] = [
        'description' => $option['label'],
      ];
    }

    $enumType = new EnumType([
      'name' => ucfirst($field->handle),
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
