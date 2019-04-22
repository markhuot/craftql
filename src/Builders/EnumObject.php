<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Request;

class EnumObject extends Schema {

    private $values = [];

    function values($values, $context=null) {
        if (is_callable($values)) {
            $values = $values($this, $context);
        }

        if (is_array($values)) {
            $this->values = $values;
        }

        return $this;
    }

    function addValue($name, $config) {
        $this->values[$name] = $config;
        return $this;
    }

    function getValues() {
        return $this->values;
    }

    function getConfig() {
        return [
            'name' => $this->getName(),
            'values' => $this->getValues(),
        ];
    }

    function getGraphQLObject() {
        return new EnumType($this->getConfig());
    }

}