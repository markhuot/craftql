<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_SchemaElementService extends BaseApplicationComponent {

  static $interface;

  function baseFields() {
    $fields = [];
    $fields['elementType'] = ['type' => Type::string()];

    return $fields;
  }

  function getInterface() {
    if (!static::$interface) {
      static::$interface = new InterfaceType([
        'name' => 'Element',
        'description' => 'A generic element in Craft',
        'fields' => $this->baseFields(),
        'resolveType' => function ($element) {
          switch ($element->elementType) {
            case 'Category':
              return ucfirst($element->group->handle);
              break;
            case 'Entry':
              return ucfirst($element->section->handle);
              break;
          }
        }
      ]);
    }

    return static::$interface;
  }

}
