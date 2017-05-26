<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaAssetSourceService extends BaseApplicationComponent {

  public $sources = [];

  function loadAllSources() {
    foreach (craft()->assetSources->allSources as $source) {
      $this->sources[$source->id] = $this->parseSourceToObject($source);
    }
  }

  function getSource($sourceId) {
    if (!isset($this->sources[$sourceId])) {
      $source = craft()->assetSources->getSourceById($sourceId);
      $this->sources[$sourceId] = $this->parseSourceToObject($source);
    }

    return $this->sources[$sourceId];
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
    $assetSourceFields = array_merge($assetSourceFields, craft()->craftQL_field->getFields($source->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($source->handle).'Assets',
      'fields' => $assetSourceFields,
    ]);
  }

}
