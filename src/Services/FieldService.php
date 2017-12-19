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
use markhuot\CraftQL\Events\GetFieldSchema as GetFieldSchemaEvent;
use GraphQL\Error\Error;


class FieldService {

  private $fieldSchemas = [];

    function getSchemaForField(\craft\base\Field $field, \markhuot\CraftQL\Request $request, $parent) {
        if (!isset($this->fieldSchemas[$field->id])) {
            $event = new GetFieldSchemaEvent;
            // if ($parent) {
            //     $event->schema = $parent->clone();
            // }
            // else {
                $event->schema = new \markhuot\CraftQL\Builders\Schema($request);
            // }
            $field->trigger('craftQlGetFieldSchema', $event);
            $this->fieldSchemas[$field->id] = $event->schema;
        }

        return $this->fieldSchemas[$field->id];
    }

    function getGraphQLMutationArgs($fieldLayoutId, $request) {
        $graphQlArgs = [];

        if ($fieldLayoutId) {
            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
            foreach ($fieldLayout->getFields() as $field) {
                // $graphQlArgs = array_merge($graphQlArgs, $this->getSchemaForField($field, $request)['args']);
            }
        }

        return $graphQlArgs;
    }

    function getFields($fieldLayoutId, $request, $parent=null) {
        $graphQlFields = [];

        if ($fieldLayoutId) {
            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
            foreach ($fieldLayout->getFields() as $field) {
                $schema = $this->getSchemaForField($field, $request, $parent);
                $graphQlFields = array_merge($graphQlFields, $schema->getFields());
            }
        }

        return $graphQlFields;
    }

    function mutateValueForField($request, $field, $value, $entry) {
        // $value = $this->getSchemaForField($field, $request)->mutate($entry, $field, $value);

        return $value;
    }

}
