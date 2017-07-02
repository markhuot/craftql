<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\fields\RichText;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\FieldTransformers\Text;

class FieldService {

  static $textTransformer;

  function __construct() {
    static::$textTransformer = new Text;
  }

  function getFields($fieldLayoutId, $tagTypes=[]) {
    $fields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = [];

      switch (get_class($field)) {
        case 'Tags': $graphQlFields = craft()->craftQL_fieldTags->getDefinition($field); break;
        case 'Date': $graphQlFields = craft()->craftQL_fieldDate->getDefinition($field); break;
        case 'Assets': $graphQlFields = craft()->craftQL_fieldAssets->getDefinition($field); break;
        case 'Entries': $graphQlFields = craft()->craftQL_fieldEntries->getDefinition($field); break;
        case 'Checkboxes': $graphQlFields = craft()->craftQL_fieldCheckboxes->getDefinition($field); break;
        case RichText::class: case 'PlainText': $graphQlFields = static::$textTransformer->getDefinition($field); break;
      }

      $fields = array_merge($fields, $graphQlFields);
    }

    return $fields;
  }

}
