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
use craft\fields\Assets as AssetsField;
use craft\fields\Color as ColorField;
use craft\fields\Dropdown as DropdownField;
use craft\fields\MultiSelect as MultiSelectField;
use craft\fields\Number as NumberField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\FieldDefinitions\Text as TextTransformer;
use markhuot\CraftQL\FieldDefinitions\RichText as RichTextTransformer;
use markhuot\CraftQL\FieldDefinitions\Checkboxes as CheckboxTransformer;
use markhuot\CraftQL\FieldDefinitions\Lightswitch as LightswitchTransformer;
use markhuot\CraftQL\FieldDefinitions\Date as DateTransformer;
use markhuot\CraftQL\FieldDefinitions\Entries as EntriesTransformer;
use markhuot\CraftQL\FieldDefinitions\Tags as TagsTransformer;
use markhuot\CraftQL\FieldDefinitions\Assets as AssetsTransformer;
use markhuot\CraftQL\FieldDefinitions\Color as ColorTransformer;
use markhuot\CraftQL\FieldDefinitions\Dropdown as DropdownTransformer;
use markhuot\CraftQL\FieldDefinitions\Number as NumberTransformer;

class FieldService {

  function getFields($fieldLayoutId) {
    $fields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = [];

      switch (get_class($field)) {
        case AssetsField::class: $transformer = Yii::$container->get(AssetsTransformer::class); break;
        case TagsField::class: $transformer = Yii::$container->get(TagsTransformer::class); break;
        case EntriesField::class: $transformer = Yii::$container->get(EntriesTransformer::class); break;
        case DateField::class: $transformer = Yii::$container->get(DateTransformer::class); break;
        case LightswitchField::class: $transformer = Yii::$container->get(LightswitchTransformer::class); break;
        case CheckboxesField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case RichTextField::class: $transformer = Yii::$container->get(RichTextTransformer::class); break;
        case PlainTextField::class: $transformer = Yii::$container->get(TextTransformer::class); break;
        case ColorField::class: $transformer = Yii::$container->get(ColorTransformer::class); break;
        case DropdownField::class: $transformer = Yii::$container->get(DropdownTransformer::class); break;
        case MultiSelectField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case NumberField::class: $transformer = Yii::$container->get(NumberTransformer::class); break;
      }

      $fields = array_merge($fields, $transformer->getDefinition($field));
    }

    return $fields;
  }

}
