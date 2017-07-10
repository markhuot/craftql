<?php

namespace markhuot\CraftQL\FieldDefinitions;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Assets extends Component {

  private $assetSources;

  function __construct(
    \markhuot\CraftQL\Services\SchemaAssetSourceService $assetSources
  ) {
    $this->assetSources = $assetSources;
  }

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf($this->assetSources->getSource(1)),
      'resolve' => function ($root, $args) use ($field) {
        return array_map(function ($asset) {
          return [
            'id' => $asset->id,
            'url' => $asset->url,
            'width' => $asset->width,
            'height' => $asset->height,
            'folder' => $asset->folder,
            'mimeType' => $asset->mimeType,
            'path' => $asset->path,
            'title' => $asset->title,
            'extension' => $asset->extension,
          ];
        }, $root->{$field->handle}->find());
      }
    ]];
  }

}
