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
use GraphQL\Error\Error;


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

  function getDateFieldDefinition($handle, $description=null, $required=false) {
    $type = \markhuot\CraftQL\Types\Timestamp::type();

    return [
      "{$handle}" => [
        'type' => $required ? Type::nonNull($type) : $type,
        'description' => $description,
        'resolve' => function ($root, $args, $context, $info) use ($handle, $required) {
          $format = 'U';

          if (isset($info->fieldNodes[0]->directives[0])) {
            $directive = $info->fieldNodes[0]->directives[0];
            if ($directive->arguments) {
              foreach ($directive->arguments as $arg) {
                $format = $arg->value->value;
              }
            }
          }

          $date = $root->{$handle};

          if ($required && !$date) {
            throw new Error("`{$handle}` is a required field but has no value");
          }

          if (!$date) {
            return null;
          }

          $date = $date->format($format);
          $cast = ($format === 'U') ? 'intval' : 'strval';
          return $cast($date);
        }
      ],
    ];
  }

}
