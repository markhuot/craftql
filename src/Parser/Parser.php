<?php

namespace markhuot\CraftQL\Parser;

use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Builders\BaseBuilder;
use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\Builders\EnumType;
use markhuot\CraftQL\Builders\InputObjectType;
use markhuot\CraftQL\Builders\ObjectType;
use markhuot\CraftQL\Request;

class Parser {

    /**
     * @todo maybe an interface?
     * @var ???
     */
    protected $parser;

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

        // @TODO maybe cache this if we're creating reflection classes for the same class multiple times
        $this->reflectedClass = new \ReflectionClass($class);

        $this->parser = $this->getParser();
        $this->parser->parse();
    }

    /**
     * Get the parser to manage this GraphQL type
     *
     * @return string
     */
    protected function getParser() {
        $doc = $this->reflectedClass->getDocComment();

        if (preg_match('/@craftql-type interface/', $doc)) {
            return new InterfaceParser($this->request, $this->reflectedClass);
        }

        if (preg_match('/@craftql-type enum/', $doc)) {
            // return EnumType::class;
        }

        if (preg_match('/@craftql-type input/', $doc)) {
            // return InputObjectType::class;
        }

        // return ObjectType::class;
    }

}