<?php

namespace markhuot\CraftQL\Craft\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Assets extends Component {

  private $assetVolumes;

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf(\markhuot\CraftQL\GraphQL\Types\Volume::interface()),
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle}->find();
      }
    ]];
  }

}
