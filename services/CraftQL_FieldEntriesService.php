<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldEntriesService extends BaseApplicationComponent {

  static $interface;

  function baseFields() {
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

    return $fields;
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
