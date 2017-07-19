<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;

class CategoryGroup {

  public $groups = [];
  private $elements;
  private $categoryGroups;

  function loadedGroups() {
    return $this->groups;
  }

  function loadAllGroups() {
    foreach (Craft::$app->categories->allGroups as $group) {
      $this->groups[$group->handle] = $this->parseGroupToObject($group);
    }
  }

  function getGroup($groupId) {
    if (!isset($this->groups[$groupId])) {
      $group = Craft::$app->categories->getGroupById($groupId);
      $this->groups[$group->handle] = $this->parseGroupToObject($group);
    }

    return $this->groups[$groupId];
  }

  function getAllGroups() {
    return $this->groups;
  }

  function parseGroupToObject($group) {
    return \markhuot\CraftQL\GraphQL\Types\CategoryGroup::make($group);
  }

}
