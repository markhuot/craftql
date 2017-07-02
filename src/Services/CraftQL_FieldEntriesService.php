<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldEntriesService extends BaseApplicationComponent {

  function getDefinition($field) {
    return [$field->handle => [
      'type' => Type::listOf(craft()->craftQL_schemaEntry->getInterface()),
    ]];
  }

}
