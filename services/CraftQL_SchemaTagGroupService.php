<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaTagGroupService extends BaseApplicationComponent {

  public $groups = [];

  function loadAllGroups() {
    foreach (craft()->tags->allTagGroups as $group) {
      $this->groups[$group->id] = $this->parseGroupToObject($group);
    }
  }

  function getGroup($groupId) {
    if (!isset($this->groups[$groupId])) {
      $group = craft()->tags->getTagGroupById($groupId);
      $this->groups[$groupId] = $this->parseGroupToObject($group);
    }

    return $this->groups[$groupId];
  }

  function parseGroupToObject($group) {
    $tagGroupFields = [];
    $tagGroupFields['id'] = ['type' => Type::int()];
    $tagGroupFields['slug'] = ['type' => Type::string()];
    $tagGroupFields = array_merge($tagGroupFields, craft()->craftQL_schemaField->getFields($group->fieldLayoutId));

    return new ObjectType([
      'name' => $group->name.' Tags',
      'fields' => $tagGroupFields,
    ]);
  }

}
