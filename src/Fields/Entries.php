<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Entries extends Component {

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface()),
      'description' => $field->instructions,
      'args' => \markhuot\CraftQL\Types\Section::args(),
      'resolve' => function ($root, $args) use ($field) {
        $criteria = $root->{$field->handle};
        foreach ($args as $key => $value) {
          $criteria->{$key} = $value;
        }
        return $criteria;
      }
    ]];
  }

}
