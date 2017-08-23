<?php

namespace markhuot\CraftQL\Services;

use Yii;
use Craft;
use craft\elements\Asset;
use craft\fields\Tags as TagsField;
use craft\fields\Table as TableField;
use craft\helpers\Assets;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Plugin;


class FieldService {

  function getArgs($fieldLayoutId, $request) {
    $graphQlArgs = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlArgs = array_merge($graphQlArgs, $field->getGraphQLMutationArgs($request));
    }

    return $graphQlArgs;
  }

  function getFields($fieldLayoutId, $request) {
    $graphQlFields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = array_merge($graphQlFields, $field->getGraphQLQueryFields($request));
    }


    return $graphQlFields;
  }

  function getDateFieldDefinition($handle) {
    return [
      "{$handle}" => [
        'type' => Type::nonNull(\markhuot\CraftQL\Types\Timestamp::type()),
        'resolve' => function ($root, $args, $context, $info) use ($handle) {
          $format = 'U';

          if (!empty($info->fieldNodes)) {
            if (!empty($info->fieldNodes[0]->directives)) {
              $directive = $info->fieldNodes[0]->directives[0];
              if ($directive->arguments) {
                foreach ($directive->arguments as $arg) {
                  $format = $arg->value->value;
                }
              }
            }
          }

          $date = $root->{$handle}->format($format);
          return ($format == 'U') ? (int)$date : (string)$date;
        }
      ],
    ];
  }

}
