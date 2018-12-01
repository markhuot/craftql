<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\Type;

class InferredArguments {

    private $arguments = [];
    private $request;
    private $context;

    function __construct($request, $context=null) {
        $this->request = $request;
        $this->context = $context;
    }

    /**
     * @param $class string
     * @return array
     */
    function parse($class) {
        $reflect = new \ReflectionClass($class);

        $this->parseProperties($reflect->getProperties());

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
        $arg->type(Type::int());

        $this->arguments[$property->getName()] = $arg;
    }

}