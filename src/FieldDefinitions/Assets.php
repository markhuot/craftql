<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Assets extends Component {

  private $assetVolumes;

  function __construct(
    \markhuot\CraftQL\Services\SchemaAssetVolumeService $assetVolumes
  ) {
    $this->assetVolumes = $assetVolumes;
  }

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf($this->assetVolumes->getInterface()),
      'resolve' => function ($root, $args) use ($field) {
        return $root->{$field->handle}->find();
      }
    ]];
  }

}
