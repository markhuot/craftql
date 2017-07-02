<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;

class SchemaTagGroupService {

  public $groups = [];

  function loadAllGroups() {
    foreach (Craft::$app->tags->allTagGroups as $group) {
      $this->groups[$group->id] = $this->parseGroupToObject($group);
    }
  }

  function getGroup($groupId) {
    if (!isset($this->groups[$groupId])) {
      $group = Craft::$app->tags->getTagGroupById($groupId);
      $this->groups[$groupId] = $this->parseGroupToObject($group);
    }

    return $this->groups[$groupId];
  }

  function parseGroupToObject($group) {
    $tagGroupFields = [];
    $tagGroupFields['id'] = ['type' => Type::int()];
    $tagGroupFields['slug'] = ['type' => Type::string()];
    $tagGroupFields = array_merge($tagGroupFields, Plugin::$field->getFields($group->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($group->handle).'Tags',
      'fields' => $tagGroupFields,
    ]);
  }

}
