<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;

class SelectOne {

  static $enums = [];

  function getEnum($field) {
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
      'type' => $this->getEnum($field),
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        return (string)$root->{$field->handle} ?: null;
      }
    ]];
  }

  function getArg($field) {
    return [
      $field->handle => ['type' => $this->getEnum($field)]
    ];
  }

}
