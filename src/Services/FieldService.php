<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\fields\PlainText as PlainTextField;
use craft\fields\RichText as RichTextField;
use craft\fields\Checkboxes as CheckboxesField;
use craft\fields\Lightswitch as LightswitchField;
use craft\fields\Date as DateField;
use craft\fields\Entries as EntriesField;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\FieldDefinitions\Text as TextTransformer;
use markhuot\CraftQL\FieldDefinitions\RichText as RichTextTransformer;
use markhuot\CraftQL\FieldDefinitions\Checkboxes as CheckboxTransformer;
use markhuot\CraftQL\FieldDefinitions\Lightswitch as LightswitchTransformer;
use markhuot\CraftQL\FieldDefinitions\Date as DateTransformer;
use markhuot\CraftQL\FieldDefinitions\Entries as EntriesTransformer;
use yii\base\Component;

class FieldService extends Component {

  private $textTransformer;
  private $richTextTransformer;
  private $checkboxTransformer;
  private $lightswitchTransformer;
  private $dateTransformer;
  private $entriesTransformer;

  function __construct(
    TextTransformer $textTransformer,
    RichTextTransformer $richTextTransformer,
    CheckboxTransformer $checkboxTransformer,
    LightswitchTransformer $lightswitchTransformer,
    DateTransformer $dateTransformer,
    EntriesTransformer $entriesTransformer
  ) {
    $this->textTransformer = $textTransformer;
    $this->richTextTransformer = $richTextTransformer;
    $this->checkboxTransformer = $checkboxTransformer;
    $this->lightswitchTransformer = $lightswitchTransformer;
    $this->dateTransformer = $dateTransformer;
    $this->entriesTransformer = $entriesTransformer;
  }

  function getFields($fieldLayoutId) {
    $fields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = [];

      switch (get_class($field)) {
        case 'Tags': $graphQlFields = craft()->craftQL_fieldTags->getDefinition($field); break;
        case 'Assets': $graphQlFields = craft()->craftQL_fieldAssets->getDefinition($field); break;
        case EntriesField::class: $graphQlFields = $this->entriesTransformer->getDefinition($field); break;
        case DateField::class: $graphQlFields = $this->dateTransformer->getDefinition($field); break;
        case LightswitchField::class: $graphQlFields = $this->lightswitchTransformer->getDefinition($field); break;
        case CheckboxesField::class: $graphQlFields = $this->checkboxTransformer->getDefinition($field); break;
        case RichTextField::class: $graphQlFields = $this->richTextTransformer->getDefinition($field); break;
        case PlainTextField::class: $graphQlFields = $this->textTransformer->getDefinition($field); break;
      }

      $fields = array_merge($fields, $graphQlFields);
    }

    return $fields;
  }

}
