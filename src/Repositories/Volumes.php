<?php

namespace markhuot\CraftQL\Repositories;

use Craft;

class Volumes {

  public $volumes = [];

  function load() {
      foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
          $this->volumes[$volume->id] = $volume;
          if (!empty($volume->uid)) {
              $this->volumes[$volume->uid] = $volume;
          }
      }
  }

  function get($id) {
      return $this->volumes[$id];
  }

  function all() {
      return $this->volumes;
  }

}
