<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class SchemaAssetVolumeService extends Component {

  public $volumes = [];
  private $fields;

  function __construct(
    \markhuot\CraftQL\Services\FieldService $fields
  ) {
    $this->fields = $fields;
  }

  function loadAllVolumes() {
    foreach (Craft::$app->volumes->getAllVolumes() as $source) {
      $this->volumes[$source->id] = $this->parseVolumeToObject($source);
    }
  }

  function getVolume($volumeId) {
    if (!isset($this->volumes[$volumeId])) {
      $source = Craft::$app->volumes->getVolumeById($volumeId);
      $this->volumes[$volumeId] = $this->parseVolumeToObject($source);
    }

    return $this->volumes[$volumeId];
  }

  function parseVolumeToObject($source) {
    $assetSourceFields = [];
    $assetSourceFields['id'] = ['type' => Type::int()];
    $assetSourceFields['uri'] = ['type' => Type::string()];
    $assetSourceFields['width'] = ['type' => Type::string()];
    $assetSourceFields['height'] = ['type' => Type::string()];
    $assetSourceFields['folder'] = ['type' => Type::string()];
    $assetSourceFields['mimeType'] = ['type' => Type::string()];
    $assetSourceFields['title'] = ['type' => Type::string()];
    $assetSourceFields['extension'] = ['type' => Type::string()];
    $assetSourceFields['filename'] = ['type' => Type::string()];
    $assetSourceFields = array_merge($assetSourceFields, $this->fields->getFields($source->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($source->handle).'Assets',
      'fields' => $assetSourceFields,
    ]);
  }

}
