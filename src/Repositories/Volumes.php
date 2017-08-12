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

  function load() {
    foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
      $this->volumes[$volume->id] = $volume;
    }
  }

  function get($id) {
    return $this->volumes[$id];
  }

  function all() {
      return $this->volumes;
  }

}
