<?php

namespace markhuot\CraftQL\services;

use Craft;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;

class SchemaCategoryGroupService {

  static $interface;
  static $baseFields;
  public $groups = [];
  private $fields;
  private $elements;
  private $categoryGroups;

  function __construct(
    \markhuot\CraftQL\Services\FieldService $fields
  ) {
    $this->fields = $fields;
  }

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

  function parseGroupToObject($group) {
    $fields = $this->baseFields();
    $fields = array_merge($fields, $this->fields->getFields($group->fieldLayoutId));

    return new ObjectType([
      'name' => ucfirst($group->handle),
      'interfaces' => [$this->getInterface(), $this->elements->getInterface()],
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
          return $this->getGroup($category->group->handle);
        }
      ]);
    }

    return static::$interface;
  }

}
