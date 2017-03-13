<?php

namespace Craft;

use GraphQL\Type\Definition\Type;

class CraftQL_FieldService extends BaseApplicationComponent {

  function getFields($fieldLayoutId, $tagTypes=[]) {
    $fields = [];

    $fieldLayout = craft()->fields->getLayoutById($fieldLayoutId);
    $fieldPivots = $fieldLayout->getFields();
    foreach ($fieldPivots as $fieldPivot) {
      $field = $fieldPivot->getField();

      switch ($field->type) {
        case 'Tags': $fields[$field->handle] = craft()->craftQL_fieldTags->getDefinition($field); break;
        case 'Date': $fields[$field->handle] = craft()->craftQL_fieldDate->getDefinition($field); break;
        case 'Assets': $fields[$field->handle] = craft()->craftQL_fieldAssets->getDefinition($field); break;
        case 'Entries': $fields[$field->handle] = craft()->craftQL_fieldEntries->getDefinition($field); break;
        case 'Checkboxes': $fields[$field->handle] = craft()->craftQL_fieldCheckboxes->getDefinition($field); break;
        case 'RichText': case 'PlainText':
          $fields[$field->handle] = [
          'type' => Type::string(),
        ];
      }
    }

    return $fields;
  }

}
