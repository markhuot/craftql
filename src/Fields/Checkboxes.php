<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class Checkboxes {

  static $enums = [];

  function getEnumFor($field) {
    if (isset(static::$enums[$field->handle])) {
      return static::$enums[$field->handle];
    }

    $options = [];
    foreach ($field['settings']['options'] as $option) {
      $options[$option['value']] = [
        'description' => $option['label'],
      ];
    }

    return static::$enums[$field->handle] = new EnumType([
      'name' => ucfirst($field->handle.'Enum'),
      'values' => $options,
    ]);
  }

  function getDefinition($field) {
    

    return [$field->handle => [
      'type' => Type::listOf($this->getEnumFor($field)),
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        $values = [];
        foreach ($root->{$field->handle} as $option) {
          $values[] = $option->value;
        }
        return $values;
      }
    ]];
  }

  function getArg($field) {
    return [
      $field->handle => ['type' => Type::listOf($this->getEnumFor($field))]
    ];
  }

}
