<?php

namespace markhuot\CraftQL\Services;

use Craft;
use markhuot\CraftQL\Events\GetFieldSchema as GetFieldSchemaEvent;


class FieldService {

    private $fieldSchemas = [];

    function getSchemaForField(\craft\base\Field $field, \markhuot\CraftQL\Request $request, $parent) {
        if (!isset($this->fieldSchemas[$field->id])) {
            $event = new GetFieldSchemaEvent;
            $event->schema = new \markhuot\CraftQL\Builders\Schema($request);
            $event->query = new \markhuot\CraftQL\Builders\Field($request, 'QUERY');
            $event->mutation = new \markhuot\CraftQL\Builders\Field($request, 'MUTATION');
            $field->trigger('craftQlGetFieldSchema', $event);
            $this->fieldSchemas[$field->id] = [
                'schema' => $event->schema,
                'query' => $event->query,
                'mutation' => $event->mutation,
            ];
        }

        return $this->fieldSchemas[$field->id];
    }

    function getQueryArguments($request) {
        $graphQlArgs = [];

        $fields = Craft::$app->fields->getAllFields();
        foreach ($fields as $field) {
            $query = $this->getSchemaForField($field, $request, null)['query'];
            $graphQlArgs = array_merge($graphQlArgs, $query->getArguments());
        }

        return $graphQlArgs;
    }

    function getMutationArguments($fieldLayoutId, $request) {
        $graphQlArgs = [];

        if ($fieldLayoutId) {
            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
            foreach ($fieldLayout->getFields() as $field) {
                $schema = $this->getSchemaForField($field, $request, null)['mutation'];
                $graphQlArgs = array_merge($graphQlArgs, $schema->getArguments());
            }
        }

        return $graphQlArgs;
    }

    function getFields($fieldLayoutId, $request, $parent=null) {
        $graphQlFields = [];

        if ($fieldLayoutId) {
            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
            foreach ($fieldLayout->getFields() as $field) {
                $schema = $this->getSchemaForField($field, $request, $parent)['schema'];
                $graphQlFields = array_merge($graphQlFields, $schema->getFields());
            }
        }

        return $graphQlFields;
    }

}
