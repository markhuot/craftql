<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Builders\ContentField;

class Enum extends ContentField {

    private $values = [];
    static $count = 0;

    function values($values) {
        if (is_callable($values)) {
            $values = $values($this->field);
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

    function getType() {
        return new EnumType([
            'name' => ucfirst($this->field->handle).'Enum',
            'values' => $this->getValues(),
        ]);
    }

}