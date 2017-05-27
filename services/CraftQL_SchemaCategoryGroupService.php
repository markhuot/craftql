<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaCategoryGroupService extends BaseApplicationComponent {

  static $interface;
  public $groups = [];

  function loadedGroups() {
    return $this->groups;
  }

  function loadAllGroups() {
    foreach (craft()->categories->allGroups as $group) {
      $this->groups[$group->handle] = $this->parseGroupToObject($group);
    }
  }

  function getGroup($groupId) {
    if (!isset($this->groups[$groupId])) {
      $group = craft()->categories->getGroupById($groupId);
      $this->groups[$group->handle] = $this->parseGroupToObject($group);
    }

    return $this->groups[$groupId];
  }

  function parseGroupToObject($group) {
    $categoryGroupFields = $this->baseFields();
    $categoryGroupFields = array_merge($categoryGroupFields, craft()->craftQL_field->getFields($group->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($group->handle),
      'interfaces' => [$this->getInterface(), craft()->craftQL_schemaElement->getInterface()],
      'fields' => $categoryGroupFields,
    ]);
  }

  function baseFields() {
    $categoryGroupFields = [];
    $categoryGroupFields['id'] = ['type' => Type::nonNull(Type::int())];
    $categoryGroupFields['title'] = ['type' => Type::nonNull(Type::string())];
    $categoryGroupFields['slug'] = ['type' => Type::string()];
    $categoryGroupFields['uri'] = ['type' => Type::string()];

    return $categoryGroupFields;
  }

  function getInterface() {
    if (!static::$interface) {
      $fields = $this->baseFields();

      static::$interface = new InterfaceType([
        'name' => 'Category',
        'description' => 'A category in Craft',
        'fields' => $fields,
        'resolveType' => function ($category) {
          return craft()->craftQL_schemaCategoryGroup->getGroup($category->group->handle);
        }
      ]);
    }

    return static::$interface;
  }

}
