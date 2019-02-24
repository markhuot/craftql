<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use craft\db\Query;
use craft\models\VolumeFolder;

class Volumes {

  public $volumes = [];

  function load() {
      $volumes = (new Query())
          ->select([
              'id',
              'dateCreated',
              'dateUpdated',
              'name',
              'handle',
              'hasUrls',
              'url',
              'sortOrder',
              'fieldLayoutId',
              'type',
              'settings',
          ])
          ->from(['{{%volumes}}'])
          ->orderBy('sortOrder asc')
          ->all();

      foreach ($volumes as $volume) {
          $this->volumes[$volume['id']] = $volume;
      }

      // foreach (Craft::$app->volumes->getAllVolumes() as $volume) {
      //     $this->volumes[$volume->id] = $volume;
      //     if (!empty($volume->uid)) {
      //         $this->volumes[$volume->uid] = $volume;
      //     }
      // }
  }

  function get($id) {
      $volume = Craft::$app->volumes->createVolume($this->volumes[$id]);
      return $volume;
  }

  function all() {
      return array_map(function ($volume) {
          return $this->get($volume['id']);
      }, $this->volumes);
  }

}
