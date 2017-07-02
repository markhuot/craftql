<?php

namespace Craft;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CraftQL_FieldTagsService extends BaseApplicationComponent {

  function getDefinition($field) {
    $source = $field->settings['source'];
    if (preg_match('/taggroup:(\d+)/', $source, $matches)) {
      $groupId = $matches[1];
      return [$field->handle => [
        'type' => Type::listOf(craft()->craftQL_schemaTagGroup->getGroup($groupId))
      ]];
    }
  }

}
