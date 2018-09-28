<?php

namespace markhuot\CraftQL\Services;

use craft\base\Field;
use craft\db\Query;
use Yii;
use Craft;
use craft\elements\Asset;
use craft\fields\Tags as TagsField;
use craft\fields\Table as TableField;
use craft\helpers\Assets;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Events\GetFieldSchema as GetFieldSchemaEvent;
use GraphQL\Error\Error;


class FieldService {

    /** @var Field[] */
    private $rawFields = [];

    /** @var Field[] */
    private $fieldsPerLayoutId = [];

    /** @var array */
    private $mapping = [];

    private $fieldSchemas = [];

    function load() {
        $this->rawFields = Craft::$app->fields->getAllFields();
        $this->mapping = [];

        $rows = (new Query())
            ->select(['layoutId', 'fieldId'])
            ->from(['{{%fieldlayoutfields}}'])
            ->all();
        foreach ($rows as $row) {
            $this->mapping[$row['layoutId']][] = $row['fieldId'];
        }
    }

    function isA($fieldHandle, $class) {
        foreach ($this->rawFields as $field) {
            if ($field->handle == $fieldHandle) {
                if (is_a($field, $class)) {
                    return true;
                }
            }
        }

        return false;
    }

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

//        $fields = Craft::$app->fields->getAllFields();
        foreach ($this->rawFields as $field) {
            $query = $this->getSchemaForField($field, $request, null)['query'];
            $graphQlArgs = array_merge($graphQlArgs, $query->getArguments());
        }

        return $graphQlArgs;
    }

    function getMutationArguments($fieldLayoutId, $request) {
        $graphQlArgs = [];

        if ($fieldLayoutId) {
//            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
//            foreach ($fieldLayout->getFields() as $field) {
            foreach ($this->getFieldsByLayoutId($fieldLayoutId) as $field) {
                $schema = $this->getSchemaForField($field, $request, null)['mutation'];
                $graphQlArgs = array_merge($graphQlArgs, $schema->getArguments());
            }
        }

        return $graphQlArgs;
    }

    function getFields($fieldLayoutId, $request, $parent=null) {
        $graphQlFields = [];

        if ($fieldLayoutId) {
//            var_dump($fieldLayout);
//            die;
//            $fieldLayout = Craft::$app->fields->getLayoutById($fieldLayoutId);
//            foreach ($fieldLayout->getFields() as $field) {
            foreach ($this->getFieldsByLayoutId($fieldLayoutId) as $field) {
                $schema = $this->getSchemaForField($field, $request, $parent)['schema'];
                $graphQlFields = array_merge($graphQlFields, $schema->getFields());
            }
        }

        return $graphQlFields;
    }

    function getAllFields($request, $parent=null) {
        $graphQlFields = [];

//        $fields = Craft::$app->fields->getAllFields();
        foreach ($this->rawFields as $field) {
            $schema = $this->getSchemaForField($field, $request, $parent)['schema'];
            $graphQlFields = array_merge($graphQlFields, $schema->getFields());
        }

        return $graphQlFields;
    }

    protected function getFieldsByLayoutId($layoutId) {
        if (isset($this->fieldsPerLayoutId[$layoutId])) {
            return $this->fieldsPerLayoutId[$layoutId];
        }

        $return = [];
        foreach ($this->rawFields as $field) {
            if (!empty($this->mapping[$layoutId])) {
                if (in_array($field->id, $this->mapping[$layoutId])) {
                    $return[] = $field;
                }
            }
        }

        return $this->fieldsPerLayoutId[$layoutId] = $return;
    }

}
