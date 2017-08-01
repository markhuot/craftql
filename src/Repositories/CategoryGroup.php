<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class CategoryGroup extends Component {

  static $groups = [];
  private $elements;
  private $categoryGroups;

  function loadedGroups() {
    return static::$groups;
  }

  function loadAllGroups() {
    foreach (Craft::$app->categories->allGroups as $group) {
      if (!isset(static::$groups[$group->id])) {
        static::$groups[$group->id] = $this->parseGroupToObject($group);
      }
    }
  }

  static function getGroup($id) {
    if (!isset(static::$groups[$id])) {
      $group = Craft::$app->categories->getGroupById($id);
      static::$groups[$group->id] = $this->parseGroupToObject($group);
    }

    return static::$groups[$id];
  }

  function getAllGroups() {
    return static::$groups;
  }

  function parseGroupToObject($group) {
    return \markhuot\CraftQL\Types\CategoryGroup::make($group);
  }

}
