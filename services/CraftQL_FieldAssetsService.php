<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldAssetsService extends BaseApplicationComponent {

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf(craft()->craftQL_schemaAssetSource->getSource(1)),
      'resolve' => function ($root, $args) use ($field) {
        $assets = $root->{$field->handle};
        $assets = array_map(function ($asset) {
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
        return $assets;
      }
    ]];
  }

}
