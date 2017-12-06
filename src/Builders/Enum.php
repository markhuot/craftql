<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Builders\Enum;

class Enum {

    private $name;
    private $values = [];
    static $type;

    function __construct($name) {
        $this->name = $name;
    }

    function addValue($name, array $config=[]) {
        if (!empty(static::$type)) {
            throw new \Exception('You can not add values to an Enum that has already been added to the GraphQL schema.');
        }

        $this->values[$name] = $config;
        return $this;
    }

    function getValues(): array {
        return $this->values;
    }

    function toArray() {
        if (static::$type) {
            return static::$type;
        }

        return static::$type = new EnumType([
            'name' => $this->name,
            'values' => $this->values,
        ]);
    }

}