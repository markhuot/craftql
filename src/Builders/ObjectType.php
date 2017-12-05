<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class ObjectType {

    private $fields = [];

    function addField($name, array $config=[]) {
        $this->fields[$name] = $config;
        return $this;
    }

    function getFields() {
        return $this->fields;
    }

    function addStringField(\craft\base\Field $field, callable $resolve=null) {
        return $this->addField($field->handle, [
            'type' => Type::string(),
            'description' => $field->instructions,
            'resolve' => $resolve,
        ]);
    }

}