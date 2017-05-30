<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaCategoryGroupService extends BaseApplicationComponent {

  static $interface;
  static $baseFields;
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
    $fields = $this->baseFields();
    $fields = array_merge($fields, craft()->craftQL_field->getFields($group->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($group->handle),
      'interfaces' => [$this->getInterface(), craft()->craftQL_schemaElement->getInterface()],
      'fields' => $fields,
    ]);
  }

  function baseFields() {
    if (!empty(static::$baseFields)) {
      return static::$baseFields;
    }

    $categoryGroup = new ObjectType([
      'name' => 'CategoryGroup',
      'fields' => [
        'id' => ['type' => Type::nonNull(Type::int())],
        'name' => ['type' => Type::nonNull(Type::string())],
        'handle' => ['type' => Type::nonNull(Type::string())],
      ],
    ]);

    $fields = [];
    $fields['id'] = ['type' => Type::nonNull(Type::int())];
    $fields['title'] = ['type' => Type::nonNull(Type::string())];
    $fields['slug'] = ['type' => Type::string()];
    $fields['uri'] = ['type' => Type::string()];
    $fields['group'] = ['type' => $categoryGroup, 'resolve' => function ($root, $args) {
      return $root->group;
    }];

    return static::$baseFields = $fields;
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
