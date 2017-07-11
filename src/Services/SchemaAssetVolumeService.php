<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class SchemaAssetVolumeService extends Component {

  static $interface;
  static $baseFields;

  public $volumes = [];
  private $fields;

  function __construct(
    \markhuot\CraftQL\Services\FieldService $fields
  ) {
    $this->fields = $fields;
  }

  function loadAllVolumes() {
    foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
      $this->volumes[$volume->handle] = $this->parseVolumeToObject($volume);
    }
  }

  function getVolume($volumeHandle) {
    if (!isset($this->volumes[$volumeHandle])) {
      $volume = Craft::$app->volumes->getVolumeById($volumeHandle);
      $this->volumes[$volumeHandle] = $this->parseVolumeToObject($volume);
    }

    return $this->volumes[$volumeHandle];
  }

  function getAllVolumes() {
    return $this->volumes;
  }

  function parseVolumeToObject($volume) {
    $fields = array_merge($this->baseFields(), $this->fields->getFields($volume->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($volume->handle).'Assets',
      'fields' => $fields,
      'interfaces' => [
          $this->getInterface(),
        ],
    ]);
  }

  function baseFields() {
    if (!empty(static::$baseFields)) {
      return static::$baseFields;
    }

    $fields = [];
    $fields['id'] = ['type' => Type::int()];
    $fields['uri'] = ['type' => Type::string()];
    $fields['url'] = ['type' => Type::string()];
    $fields['width'] = ['type' => Type::string()];
    $fields['height'] = ['type' => Type::string()];
    $fields['size'] = ['type' => Type::int()];
    $fields['folder'] = ['type' => Type::string()];
    $fields['mimeType'] = ['type' => Type::string()];
    $fields['title'] = ['type' => Type::string()];
    $fields['extension'] = ['type' => Type::string()];
    $fields['filename'] = ['type' => Type::string()];
    $fields['dateCreatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateCreated->format('U');
    }];
    $fields['dateCreated'] = ['type' => Type::nonNull(Type::string()), 'args' => [
      ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
    ], 'resolve' => function ($root, $args) {
      return $root->dateCreated->format($args['format']);
    }];
    $fields['dateUpdatedTimestamp'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format('U');
    }];
    $fields['dateUpdated'] = ['type' => Type::nonNull(Type::int()), 'args' => [
      ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
    ], 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format($args['format']);
    }];

    return static::$baseFields = $fields;
  }

  function getInterface() {
    return static::$interface ?: static::$interface = new InterfaceType([
      'name' => 'AssetInterface',
      'description' => 'An asset in Craft',
      'fields' => $this->baseFields(),
      'resolveType' => function ($asset) {
        return ucfirst($asset->getVolume()->handle).'Assets';
      }
    ]);
  }

}
