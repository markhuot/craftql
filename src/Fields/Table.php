<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Table extends Component {

    private $tagGroups;
    static $objects = [];
    static $inputs = [];

    function getFields($field, $input=false) {
        $fields = [];
        foreach ($field->columns as $key => $column) {
            switch ($column['type']) {
                case 'number':
                    $fields[$input ? $key : $column['handle']] = ['type' => Type::float(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
                        return $root[$column['handle']];
                    }];
                    break;
                case 'checkbox':
                case 'lightswitch':
                    $fields[$input ? $key : $column['handle']] = ['type' => Type::boolean(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
                        return $root[$column['handle']];
                    }];
                    break;
                default:
                    $fields[$input ? $key : $column['handle']] = ['type' => Type::string(), 'description' => $column['heading'], 'resolve' => function ($root, $args) use ($column) {
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

    function getDefinition($field) {
        return [$field->handle => [
            'type' => Type::listOf($this->getGraphQlObject($field)),
            'description' => $field->instructions,
        ]];
    }

    function getGraphQLInput($field) {
        if (isset(static::$inputs[$field->handle])) {
            return static::$inputs[$field->handle];
        }

        return static::$inputs[$field->handle] = new InputObjectType([
            'name' => ucfirst($field->handle).'TableInput',
            'fields' => $this->getFields($field, true),
        ]);
    }

  function getArg($field) {
    return [
        $field->handle => ['type' => Type::listOf($this->getGraphQlInput($field))]
    ];
  }

  function upsert($field, $values) {

  }

}
