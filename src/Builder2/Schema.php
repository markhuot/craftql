<?php

namespace markhuot\CraftQL\Builder2;

class Schema {

    private $types;
    private $query;
    private $mutation;

    function addType($type) {
        $this->types[] = $type;
    }

}