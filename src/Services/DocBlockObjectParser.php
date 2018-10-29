<?php

namespace markhuot\CraftQL\Services;

use GraphQL\Type\Definition\ObjectType;

class DocBlockObjectParser {

    function parse($class) {
        $reflect = new \ReflectionClass($class);
        $this->parseProperties($reflect->getProperties());
    }

    function parseProperties($properties) {
        foreach ($properties as $property) {
            $this->parseProperty($property);
        }
    }

    function parseProperty($property) {

    }

}