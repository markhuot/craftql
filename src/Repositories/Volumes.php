<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class Volumes {

  public $volumes = [];

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
    return \markhuot\CraftQL\GraphQL\Types\Volume::make($volume);
  }

}
