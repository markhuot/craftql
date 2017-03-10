<?php

namespace Craft;

use GraphQL\Type\Definition\Type;

class CraftQL_SchemaFieldService extends BaseApplicationComponent {

  function getFields($fieldLayoutId, $tagTypes=[]) {
    $fields = [];

    $fieldLayout = craft()->fields->getLayoutById($fieldLayoutId);
    $fieldPivots = $fieldLayout->getFields();
    foreach ($fieldPivots as $fieldPivot) {
      $field = $fieldPivot->getField();
      if ($field->type == 'Tags') {
        $fields[$field->handle] = [
          'type' => Type::listOf(craft()->craftQL_schemaTagGroup->getGroup($field->groupId))
        ];
      }
      else if ($field->type == 'Date') {
        $fields[$field->handle] = [
          'type' => Type::int(),
          'resolve' => function ($root, $args) use ($field) {
            return $root->{$field->handle}->format('U');
          }
        ];
      }
      else {
        $fields[$field->handle] = [
          'type' => Type::string(),
        ];
      }
    }

    return $fields;
  }

}
