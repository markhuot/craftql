<?php

namespace markhuot\CraftQL\Services;

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
use yii\helpers\ArrayHelper;


class FieldService {

    private $fieldSchemas = [];
    private $args = null;

    /** @var Field[] */
    private $rawFields = [];

    /** @var Field[] */
    private $fieldsPerLayoutId = [];

    /** @var array */
    private $mapping = [];

    function load() {
        $fieldQuery = (new Query())
            ->select([
                'fields.id',
                'fields.dateCreated',
                'fields.dateUpdated',
                'fields.groupId',
                'fields.name',
                'fields.handle',
                'fields.context',
                'fields.instructions',
                'fields.translationMethod',
                'fields.translationKeyFormat',
                'fields.type',
                'fields.settings'
            ])
            ->from(['{{%fields}} fields'])
            ->orderBy(['fields.name' => SORT_ASC, 'fields.handle' => SORT_ASC]);

        $this->rawFields = ArrayHelper::index(array_map(function ($field) {
            return Craft::$app->fields->createField($field);
        }, $fieldQuery->all()), 'id');

        $this->mapping = [];
        $rows = (new Query())
            ->select(['layoutId', 'tabId', 'fieldId', 'sortOrder'])
            ->from(['{{%fieldlayoutfields}}'])
            ->all();
        foreach ($rows as $row) {
            $this->mapping[$row['layoutId']][$row['tabId'].':'.$row['sortOrder']] = $this->rawFields[$row['fieldId']];
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
        if ($this->args !== null) {
            return $this->args;
        }

        $graphQlArgs = [];

        foreach ($this->rawFields as $field) {
            $query = $this->getSchemaForField($field, $request, null)['query'];
            $graphQlArgs = array_merge($graphQlArgs, $query->getArguments());
        }

        return $this->args = $graphQlArgs;
    }

    function getMutationArguments($fieldLayoutId, $request) {
        $graphQlArgs = [];

        if ($fieldLayoutId) {
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
            foreach ($this->getFieldsByLayoutId($fieldLayoutId) as $field) {
                $schema = $this->getSchemaForField($field, $request, $parent)['schema'];
                $graphQlFields = array_merge($graphQlFields, $schema->getFields());
            }
        }

        return $graphQlFields;
    }

    function getAllFields($request, $parent=null) {
        $graphQlFields = [];
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

        // foreach ($this->rawFields as $field) {
        //     if (!empty($this->mapping[$layoutId])) {
        //         if ($field = $this->rawFields) {
        //             $fieldConfig = $this->mapping[$layoutId][$key];
        //             $return[$fieldConfig['sortOrder']] = $fieldConfig['fieldId'];
        //         }
        //     }
        // }

        return $this->fieldsPerLayoutId[$layoutId] = array_values(@$this->mapping[$layoutId] ?: []);
    }

}
