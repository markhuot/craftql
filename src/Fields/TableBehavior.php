<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Behavior;

class TableBehavior extends Behavior
{
    static $objects = [];
    static $inputs = [];

    function getGraphQLInput($field) {
        if (isset(static::$inputs[$field->handle])) {
            return static::$inputs[$field->handle];
        }

        return static::$inputs[$field->handle] = new InputObjectType([
            'name' => ucfirst($field->handle).'TableInput',
            'fields' => $this->getFields($field),
        ]);
    }

    public function getGraphQLMutationArgs() {
        $field = $this->owner;

        return [
            $field->handle => ['type' => Type::listOf($this->getGraphQlInput($field))]
        ];
    }

    function getFields($field) {
        $fields = [];
        foreach ($field->columns as $key => $column) {
            switch ($column['type']) {
                case 'number':
                    $fields[$column['handle']] = ['type' => Type::float(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
                        return $root[$column['handle']];
                    }];
                    break;
                case 'checkbox':
                case 'lightswitch':
                    $fields[$column['handle']] = ['type' => Type::boolean(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
                        return $root[$column['handle']];
                    }];
                    break;
                default:
                    $fields[$column['handle']] = ['type' => Type::string(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
                        return $root[$column['handle']];
                    }];
            }
        }
        return $fields;
    }

    function getGraphQlObject($field) {
        if (isset(static::$objects[$field->handle])) {
            return static::$objects[$field->handle];
        }

        return static::$objects[$field->handle] = new ObjectType([
            'name' => ucfirst($field->handle).'Table',
            'fields' => $this->getFields($field),
        ]);
    }

    public function getGraphQLQueryFields($token) {
        $field = $this->owner;

        return [
            $field->handle => [
                'type' => Type::listOf($this->getGraphQlObject($field)),
                'description' => $field->instructions,
            ]
        ];
    }

    function getKey($handle) {
        $field = $this->owner;

        foreach ($field->columns as $key => $column) {
            if ($column['handle'] == $handle) {
                return $key;
            }
        }

        return false;
    }

    public function upsert($values) {
        foreach ($values as &$row) {
            foreach ($row as $handle => $value) {
                $newKey = $this->getKey($handle);
                $row[$newKey] = $value;
                unset($row[$handle]);
            }
        }

        return $values;
    }

}