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

      $graphQlFields = [];
      switch ($field->type) {
        case 'Tags': $graphQlFields = craft()->craftQL_fieldTags->getDefinition($field); break;
        case 'Date': $graphQlFields = craft()->craftQL_fieldDate->getDefinition($field); break;
        case 'Assets': $graphQlFields = craft()->craftQL_fieldAssets->getDefinition($field); break;
        case 'Entries': $graphQlFields = craft()->craftQL_fieldEntries->getDefinition($field); break;
        case 'Checkboxes': $graphQlFields = craft()->craftQL_fieldCheckboxes->getDefinition($field); break;
        case 'RichText':
        case 'PlainText':
          $graphQlFields = craft()->craftQL_fieldText->getDefinition($field); break;
      }

      $fields = array_merge($fields, $graphQlFields);
    }

    return $fields;
  }

}
