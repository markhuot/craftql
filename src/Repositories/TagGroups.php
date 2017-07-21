<?php

namespace markhuot\CraftQL\Repositories;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use yii\base\Component;

class TagGroups extends Component {

  static $groups = [];

  function loadAllGroups() {
    foreach (Craft::$app->tags->allTagGroups as $group) {
      static::$groups[$group->id] = $this->parseGroupToObject($group);
    }
  }

  function getGroup($groupId) {
    if (!isset(static::$groups[$groupId])) {
      $group = Craft::$app->tags->getTagGroupById($groupId);
      static::$groups[$groupId] = $this->parseGroupToObject($group);
    }

    return static::$groups[$groupId];
  }

  function parseGroupToObject($group) {
    return \markhuot\CraftQL\Types\TagGroup::make($group);
  }

}
