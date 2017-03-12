<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldEntriesService extends BaseApplicationComponent {

  static $interface;

  function getInterface() {
    if (!static::$interface) {
      static::$interface = new InterfaceType([
        'name' => 'Entry',
        'description' => 'An entry in Craft',
        'fields' => [
            'id' => ['type' => Type::nonNull(Type::string())],
            'name' => ['type' => Type::nonNull(Type::string())],
            'title' => ['type' => Type::nonNull(Type::string())],
        ],
        'resolveType' => function ($entry) {
          $type = craft()->craftQL_schemaSection->getSection($entry->section->id);
          // var_dump($type);
          // die;
          return craft()->craftQL_schemaSection->getSection($entry->section->id);
        }
      ]);
    }

    return static::$interface;
  }

  function getDefinition($field) {
    $entryInterface = $this->getInterface();

    return [
      'type' => Type::listOf($entryInterface),
      'resolve' => function ($root, $args) use ($field) {
        $entries = $root->{$field->handle};
        return $entries;
      }
    ];
  }

}
