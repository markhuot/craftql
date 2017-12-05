<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class ObjectType {

    private $fields = [];

    function addField($name, array $config=[]) {
        $this->fields[$name] = $config;
    }

    function addStringField(\craft\base\Field $field) {
        $this->addField($field->handle, [
            'type' => Type::string(),
            'description' => $field->instructions,
        ]);
    }

    function getFields() {
        return $this->fields;
    }

}