<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class InferredArguments extends InferredBase {

    private $arguments = [];

    /**
     * @param $class string
     * @return array
     */
    function parse($class) {
        parent::parse($class);

        $this->parseProperties($this->reflectedClass->getProperties());

        return $this->arguments;
    }

    /**
     * @param $properties \ReflectionProperty[]
     */
    function parseProperties($properties) {
        foreach ($properties as $property) {
            $this->parseProperty($property);
        }
    }

    function parseProperty(\ReflectionProperty $property) {
        $arg = new Argument($this->request, $property->getName());
        list($type, $isList) = $this->getTypeFromDoc($property);
        $arg->type($type)->lists($isList);

        $this->arguments[$property->getName()] = $arg;
    }

}