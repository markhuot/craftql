<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\UnionType;
use yii\base\Component;

class Assets extends Component {

  private $assetVolumes;
  static $inputObjects = [];

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf(\markhuot\CraftQL\Types\Volume::interface()),
      'description' => $field->instructions,
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle}->all();
      }
    ]];
  }

  function getInputObject($field) {
    if (isset(static::$inputObjects[$field->handle])) {
      return static::$inputObjects[$field->handle];
    }

    return static::$inputObjects[$field->handle] = new InputObjectType([
      'name' => ucfirst($field->handle).'AssetInput',
      'fields' => [
        'id' => ['type' => Type::int()],
        'url' => ['type' => Type::string()],
      ],
    ]);
  }

  function getArg($field) {
    return [
      $field->handle => ['type' => Type::listOf($this->getInputObject($field))],
    ];
  }

}
