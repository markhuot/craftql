<?php

namespace markhuot\CraftQL\Services;

use Yii;
use Craft;
use craft\elements\Asset;
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
use craft\fields\Matrix as MatrixField;
use craft\fields\Table as TableField;
use craft\helpers\Assets;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\Fields\RichText as RichTextTransformer;
use markhuot\CraftQL\Fields\Checkboxes as CheckboxTransformer;
use markhuot\CraftQL\Fields\Lightswitch as LightswitchTransformer;
use markhuot\CraftQL\Fields\Date as DateTransformer;
use markhuot\CraftQL\Fields\Entries as EntriesTransformer;
use markhuot\CraftQL\Fields\Tags as TagsTransformer;
use markhuot\CraftQL\Fields\Assets as AssetsTransformer;
use markhuot\CraftQL\Fields\SelectOne as SelectOneTransformer;
use markhuot\CraftQL\Fields\Number as NumberTransformer;
use markhuot\CraftQL\Fields\Categories as CategoriesTransformer;
use markhuot\CraftQL\Fields\PositionSelect as PositionSelectTransformer;
use markhuot\CraftQL\Fields\Table as TableTransformer;
use markhuot\CraftQL\Fields\Matrix as MatrixTransformer;
use markhuot\CraftQL\Fields\Unknown as UnknownTransformer;

class FieldService {

  function getTransformer($field) {
    switch (get_class($field)) {
        case AssetsField::class: $transformer = Yii::$container->get(AssetsTransformer::class); break;
        case TagsField::class: $transformer = Yii::$container->get(TagsTransformer::class); break;
        case EntriesField::class: $transformer = Yii::$container->get(EntriesTransformer::class); break;
        case DateField::class: $transformer = Yii::$container->get(DateTransformer::class); break;
        case LightswitchField::class: $transformer = Yii::$container->get(LightswitchTransformer::class); break;
        case CheckboxesField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case RichTextField::class: $transformer = Yii::$container->get(RichTextTransformer::class); break;
        case MultiSelectField::class: $transformer = Yii::$container->get(CheckboxTransformer::class); break;
        case NumberField::class: $transformer = Yii::$container->get(NumberTransformer::class); break;
        case CategoriesField::class: $transformer = Yii::$container->get(CategoriesTransformer::class); break;
        case PositionSelectField::class: $transformer = Yii::$container->get(PositionSelectTransformer::class); break;
        case TableField::class: $transformer = Yii::$container->get(TableTransformer::class); break;
        case MatrixField::class: $transformer = Yii::$container->get(MatrixTransformer::class); break;
        case RadioButtonsField::class: $transformer = Yii::$container->get(SelectOneTransformer::class); break;
        case DropdownField::class: $transformer = Yii::$container->get(SelectOneTransformer::class); break;
        
        case ColorField::class:
        case PlainTextField::class:
        default:
          $transformer = Yii::$container->get(UnknownTransformer::class); break;
      }

      return $transformer;
  }

  function getArg($field) {
    return $this->getTransformer($field)->getArg($field);
  }

  function getArgs($fieldLayoutId) {
    $graphQlFields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = array_merge($graphQlFields, $this->getArg($field));
    }

    return $graphQlFields;
  }

  function getField($field) {
    return $this->getTransformer($field)->getDefinition($field);
  }

  function getFields($fieldLayoutId) {
    $graphQlFields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      if ($field->hasMethod('getGraphQLFieldDefinition')) {
        $graphQlFields = array_merge($graphQlFields, $field->getGraphQLFieldDefinition());
      }
      else {
        $graphQlFields = array_merge($graphQlFields, $this->getField($field));
      }
    }

    return $graphQlFields;
  }

  function upsertFieldValue($handle, $values) {
    if ($handle == 'testTableField') {
      $table->upsert($field, $values);
    }
    if ($handle == 'image') {
      $images = [];

      foreach ($values as $value) {
        if (!empty($value['id'])) {
          $images[] = $value['id'];
        }
        if (!empty($value['url'])) {
          $remoteUrl = $value['url'];
          $parts = parse_url($remoteUrl);
          $basename = basename($parts['path']);
          $filename = Assets::prepareAssetName($basename, true);

          $temp = tmpfile();
          fwrite($temp, file_get_contents($remoteUrl));
          $uploadPath = stream_get_meta_data($temp)['uri'];

          if (!pathinfo($filename, PATHINFO_EXTENSION)) {
            $mimeType = mime_content_type($uploadPath);
            $exts = \craft\helpers\FileHelper::getExtensionsByMimeType($mimeType);
            if (count($exts)) {
              $ext = $exts[count($exts)-1];
              $filename = pathinfo($filename, PATHINFO_FILENAME).'.'.$ext;
            }
          }

          $asset = new Asset();
          $asset->tempFilePath = $uploadPath;
          $asset->filename = $filename;
          $asset->volumeId = 1;
          $asset->newFolderId = 1;
          $asset->newFilename = $filename;
          $asset->newLocation = '{folder:1}'.$filename;
          $asset->avoidFilenameConflicts = true;
          $asset->setScenario(Asset::SCENARIO_CREATE);

          $result = Craft::$app->getElements()->saveElement($asset);
          if ($result) {
            $images[] = $asset->id;
          }
          else {
            throw new Exception(implode(' ', $asset->getFirstErrors()));
          }

          fclose($temp);
        }
      }

      return $images;
    }
  }

}
