<?php

namespace markhuot\CraftQL\Parser;

use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Builders\BaseBuilder;
use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\Builders\InputObjectType;
use markhuot\CraftQL\Builders\ObjectType;
use markhuot\CraftQL\Request;

class Parser {

    /**
     * @var BaseBuilder
     */
    protected $builder;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var \ReflectionClass
     */
    protected $reflectedClass;

    /**
     * Parser constructor.
     * @param $request
     * @param $class
     * @throws \ReflectionException
     */
    function __construct($request, $class) {
        $this->request = $request;
        $this->reflectedClass = new \ReflectionClass($class);

        $builderClass = $this->getBuilderClass();
        $this->builder = new $builderClass($this->request);
        $this->builder->name($this->reflectedClass->getShortName());

        $this->parseObjectTypeConfig();
        $this->parseInterfaceTypeConfig();
        $this->parseEnumTypeConfig();
    }

    /**
     * Get the GraphQL builder class to manage this PHP class
     *
     * @return string
     */
    function getBuilderClass() {
        $doc = $this->reflectedClass->getDocComment();

        if (preg_match('/@craftql-type interface/', $doc)) {
            return InterfaceType::class;
        }

        if (preg_match('/@craftql-type enum/', $doc)) {
            return EnumObject::class;
        }

        if (preg_match('/@craftql-type input/', $doc)) {
            return InputObjectType::class;
        }

        return ObjectType::class;
    }

}