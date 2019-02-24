<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\db\Query;
use craft\models\VolumeFolder;

class Volumes {

  public $volumes = [];

  function load() {
      $volumes = (new Query())
          ->select(['id'])
          ->from(['{{%volumes}}'])
          ->orderBy('sortOrder asc')
          ->all();

      // foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
      //     $this->volumes[$volume->id] = $volume;
      //     if (!empty($volume->uid)) {
      //         $this->volumes[$volume->uid] = $volume;
      //     }
      // }
  }

  function get($id) {
      $volume = new VolumeFolder($this->volumes[$id]);
      return $volume;
  }

  function all() {
      return array_map(function ($volume) {
          return $this->volumes[$volume['id']];
      }, $this->volumes);
  }

}
