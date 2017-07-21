<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use yii\base\Component;

class Table extends Component {

    private $tagGroups;
    static $objects = [];

    function getGraphQlObject($field) {
        if (isset(static::$objects[$field->handle])) {
            return static::$objects[$field->handle];
        }
        
        $fields = [];
        foreach ($field->columns as $column) {
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

        return static::$objects[$field->handle] = new ObjectType([
            'name' => ucfirst($field->handle).'Table',
            'fields' => $fields,
        ]);
    }

    function getDefinition($field) {
        return [$field->handle => [
            'type' => Type::listOf($this->getGraphQlObject($field)),
            'description' => $field->instructions,
        ]];
    }

}
