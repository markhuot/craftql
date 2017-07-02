<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;

class SchemaAssetSourceService {

  public $sources = [];

  function loadAllSources() {
    // foreach (Craft::$app->volumes->allSources as $source) {
    //   $this->sources[$source->id] = $this->parseSourceToObject($source);
    // }
  }

  function getSource($sourceId) {
    // if (!isset($this->sources[$sourceId])) {
    //   $source = Craft::$app->volumes->getSourceById($sourceId);
    //   $this->sources[$sourceId] = $this->parseSourceToObject($source);
    // }

    // return $this->sources[$sourceId];
  }

  function parseSourceToObject($source) {
    $assetSourceFields = [];
    $assetSourceFields['id'] = ['type' => Type::int()];
    $assetSourceFields['url'] = ['type' => Type::string()];
    $assetSourceFields['width'] = ['type' => Type::string()];
    $assetSourceFields['height'] = ['type' => Type::string()];
    $assetSourceFields['folder'] = ['type' => Type::string()];
    $assetSourceFields['mimeType'] = ['type' => Type::string()];
    $assetSourceFields['path'] = ['type' => Type::string()];
    $assetSourceFields['title'] = ['type' => Type::string()];
    $assetSourceFields['extension'] = ['type' => Type::string()];
    $assetSourceFields = array_merge($assetSourceFields, Plugin::$fieldService->getFields($source->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($source->handle).'Assets',
      'fields' => $assetSourceFields,
    ]);
  }

}
