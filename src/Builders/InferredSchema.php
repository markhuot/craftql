<?php

namespace markhuot\CraftQL\Builders;

use markhuot\CraftQL\Request;

class InferredSchema {

    /** @var BaseBuilder */
    private $type;

    /** @var Request */
    private $request;

    function __construct($request) {
        $this->request = $request;
    }

    function parse($class) {
        $reflect = new \ReflectionClass($class);

        $type = Schema::class;
        $doc = $reflect->getDocComment();
        if (preg_match('/@craftql-type interface/', $doc)) {
            $type = InterfaceBuilder::class;
        }

        $this->type = new $type($this->request);
        $this->type->name($reflect->getShortName());

        $this->parseProperties($reflect->getProperties());
        $this->parseMethods($reflect->getMethods());

        return $this->type;
    }
    /**
     * @param $properties \ReflectionProperty[]
     */
    function parseProperties($properties) {
        foreach ($properties as $property) {
            $this->parseProperty($property);
        }
    }
    /**
     * @param $property \ReflectionProperty
     */
    function parseProperty($property) {
        if (!$property->isPublic()) {
            return;
        }

        $this->type->addStringField($property->getName());
    }
    /**
     * @param $methods \ReflectionMethod[]
     */
    function parseMethods($methods) {
        foreach ($methods as $method) {
            $this->parseMethod($method);
        }
    }
    /**
     * @param $method \ReflectionMethod
     */
    function parseMethod($method) {
        if (preg_match('/^get([A-Z][a-zA-Z0-9_]*)$/', $method->getName(), $matches)) {
            $name = lcfirst($matches[1]);
            $this->type->addStringField($name);
        }
    }

}