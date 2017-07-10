<?php

namespace markhuot\CraftQL\Services;

use Yii;
use Craft;
use craft\fields\PlainText as PlainTextField;
use craft\fields\RichText as RichTextField;
use craft\fields\Checkboxes as CheckboxesField;
use craft\fields\Lightswitch as LightswitchField;
use craft\fields\Date as DateField;
use craft\fields\Entries as EntriesField;
use craft\fields\Tags as TagsField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\FieldDefinitions\Text as TextTransformer;
use markhuot\CraftQL\FieldDefinitions\RichText as RichTextTransformer;
use markhuot\CraftQL\FieldDefinitions\Checkboxes as CheckboxTransformer;
use markhuot\CraftQL\FieldDefinitions\Lightswitch as LightswitchTransformer;
use markhuot\CraftQL\FieldDefinitions\Date as DateTransformer;
use markhuot\CraftQL\FieldDefinitions\Entries as EntriesTransformer;
use markhuot\CraftQL\FieldDefinitions\Tags as TagsTransformer;

class FieldService {

  private $textTransformer;
  private $richTextTransformer;
  private $checkboxTransformer;
  private $lightswitchTransformer;
  private $dateTransformer;
  private $entriesTransformer;
  private $tagsTransformer;

  function getFields($fieldLayoutId) {
    $fields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = [];

      switch (get_class($field)) {
        // case 'Assets': $graphQlFields = craft()->craftQL_fieldAssets->getDefinition($field); break;
        case TagsField::class: $transformer = Yii::$container->get(TagsTransformer::class); break;
        case EntriesField::class: $transformer = Yii::$container->get(EntriesTransformer::class); break;
        case DateField::class: $transformer = Yii::$container->get(DateTransformer::class); break;
        case LightswitchField::class: $transformer = Yii::$container->get(LightswitchTransformer::class); break;
        case CheckboxesField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case RichTextField::class: $transformer = Yii::$container->get(RichTextTransformer::class); break;
        case PlainTextField::class: $transformer = Yii::$container->get(TextTransformer::class); break;
      }

      $fields = array_merge($fields, $transformer->getDefinition($field));
    }

    return $fields;
  }

}
