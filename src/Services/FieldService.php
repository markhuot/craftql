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
use craft\fields\RadioButtons as RadioButtonsField;
use craft\fields\Categories as CategoriesField;
use craft\fields\PositionSelect as PositionSelectField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\Fields\Text as TextTransformer;
use markhuot\CraftQL\Fields\RichText as RichTextTransformer;
use markhuot\CraftQL\Fields\Checkboxes as CheckboxTransformer;
use markhuot\CraftQL\Fields\Lightswitch as LightswitchTransformer;
use markhuot\CraftQL\Fields\Date as DateTransformer;
use markhuot\CraftQL\Fields\Entries as EntriesTransformer;
use markhuot\CraftQL\Fields\Tags as TagsTransformer;
use markhuot\CraftQL\Fields\Assets as AssetsTransformer;
use markhuot\CraftQL\Fields\Color as ColorTransformer;
use markhuot\CraftQL\Fields\Dropdown as DropdownTransformer;
use markhuot\CraftQL\Fields\Number as NumberTransformer;
use markhuot\CraftQL\Fields\Categories as CategoriesTransformer;
use markhuot\CraftQL\Fields\PositionSelect as PositionSelectTransformer;
use markhuot\CraftQL\Fields\RadioButton as RadioButtonTransformer;

class FieldService {

  function getAllFieldArgs() {
    $fields = [
      'id' => Type::int(),
      'sectionId' => Type::int(),
      'typeId' => Type::int(),
      'authorId' => Type::int(),
      'title' => Type::string(),
    ];
    $allFields = Craft::$app->fields->getAllFields();
    foreach ($allFields as $field) {
      $fields[$field->handle] = Type::string();
    }
    return $fields;
  }

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
        case RadioButtonsField::class: $transformer = Yii::$container->get(RadioButtonTransformer::class); break;
        case MultiSelectField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case NumberField::class: $transformer = Yii::$container->get(NumberTransformer::class); break;
        case CategoriesField::class: $transformer = Yii::$container->get(CategoriesTransformer::class); break;
        case PositionSelectField::class: $transformer = Yii::$container->get(PositionSelectTransformer::class); break;
      }

      $fields = array_merge($fields, $transformer->getDefinition($field));
    }

    return $fields;
  }

}
