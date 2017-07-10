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
      'type' => Type::listOf($this->assetVolumes->getVolume(1)),
      'resolve' => function ($root, $args) use ($field) {
        return array_map(function ($asset) {
          // var_dump($asset);
          // die;
          return [
            'id' => $asset->id,
            'uri' => $asset->getUri(),
            'width' => $asset->width,
            'height' => $asset->height,
            'folder' => $asset->folder,
            'mimeType' => $asset->mimeType,
            'title' => $asset->title,
            'extension' => $asset->extension,
            'filename' => $asset->filename,
          ];
        }, $root->{$field->handle}->find());
      }
    ]];
  }

}
