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

  function getArgs($fieldLayoutId) {
    $graphQlArgs = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlArgs = array_merge($graphQlArgs, $field->getGraphQLMutationArgs());
    }

    return $graphQlArgs;
  }

  function getFields($fieldLayoutId) {
    $graphQlFields = [];

    $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
    foreach ($fieldLayout->getFields() as $field) {
      $graphQlFields = array_merge($graphQlFields, $field->getGraphQLQueryFields());
    }

    return $graphQlFields;
  }

  function getDateFieldDefinition($handle) {
    return [
      "{$handle}Timestamp" => ['type' => Type::nonNull(Type::int()), 'resolve' => function ($root, $args) use ($handle) {
            return $root->{$handle}->format('U');
      }],
      "{$handle}" => ['type' => Type::nonNull(Type::string()), 'args' => [
          ['name' => 'format', 'type' => Type::string(), 'defaultValue' => 'r']
      ], 'resolve' => function ($root, $args) use ($handle) {
          return $root->{$handle}->format($args['format']);
      }],
    ];
  }

}
