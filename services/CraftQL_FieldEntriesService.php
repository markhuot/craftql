<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldEntriesService extends BaseApplicationComponent {

  static $interface;
  static $baseFields;

  function baseFields() {
    if (!empty(static::$baseFields)) {
      return static::$baseFields;
    }

    $sectionType = new ObjectType([
      'name' => 'Section',
      'fields' => [
        'id' => ['type' => Type::nonNull(Type::int())],
        'structureId' => ['type' => Type::nonNull(Type::int())],
        'name' => ['type' => Type::nonNull(Type::string())],
        'handle' => ['type' => Type::nonNull(Type::string())],
        'type' => ['type' => Type::nonNull(Type::string())],
        'template' => ['type' => Type::nonNull(Type::string())],
        'maxLevels' => ['type' => Type::nonNull(Type::int())],
        'hasUrls' => ['type' => Type::nonNull(Type::boolean())],
        'enableVersioning' => ['type' => Type::nonNull(Type::boolean())],
      ],
    ]);

    $fields = [];
    $fields['id'] = ['type' => Type::nonNull(Type::int())];
    $fields['title'] = ['type' => Type::nonNull(Type::string())];
    $fields['slug'] = ['type' => Type::nonNull(Type::string())];
    $fields['dateCreated'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateCreated->format('U');
    }];
    $fields['dateUpdated'] = ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) {
      return $root->dateUpdated->format('U');
    }];
    $fields['expiryDate'] = ['type' => Type::int(), 'resolve' => function ($root, $args) {
      return $root->expiryDate->format('U');
    }];
    $fields['enabled'] = ['type' => Type::nonNull(Type::boolean())];
    $fields['status'] = ['type' => Type::nonNull(Type::string())];
    $fields['uri'] = ['type' => Type::string()];
    $fields['url'] = ['type' => Type::string()];
    $fields['section'] = ['type' => $sectionType, 'resolve' => function ($root, $args) {
      return $root->section;
    }];

    return static::$baseFields = $fields;
  }

  function getInterface() {
    if (!static::$interface) {
      $fields = $this->baseFields();

      static::$interface = new InterfaceType([
        'name' => 'Entry',
        'description' => 'An entry in Craft',
        'fields' => $fields,
        'resolveType' => function ($entry) {
          return craft()->craftQL_schemaSection->getSection($entry->section->handle);
        }
      ]);
    }

    return static::$interface;
  }

  function getDefinition($field) {
    $entryInterface = $this->getInterface();

    return [
      'type' => Type::listOf($entryInterface),
    ];
  }

}
