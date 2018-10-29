<?php

namespace markhuot\CraftQL\Builder2;

use markhuot\CraftQL\Services\DocBlockObjectParser;

class Schema {

    private $types;
    private $query;
    private $mutation;

    function getType($className) {
        if (!empty($this->types[$className])) {
            return $this->types[$className];
        }

        return $this->types[$className] = (new DocBlockObjectParser($this))->parse($className);
    }

    function addConcreteType($interfaceClassName, $config) {
        $type = (new DocBlockObjectParser($this))->parse($interfaceClassName);
        foreach ($config as $key => $value) {
            $type->{$key} = $value;
        }

        $this->types[] = $type;
    }

    function getTypes() {
        return array_values($this->types);
    }

}