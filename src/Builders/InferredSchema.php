<?php

namespace markhuot\CraftQL\Builders;

use markhuot\CraftQL\Request;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;

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

        if ($method->getName() == 'getEdges') {
            $type = $this->getTypeFromDoc($method);
            $name = 'edges';
            $this->type->addField($name)->type((string)$type)->lists();
        }

        else if (preg_match('/^get([A-Z][a-zA-Z0-9_]*)$/', $method->getName(), $matches)) {
            $name = lcfirst($matches[1]);
            $type = $this->getTypeFromDoc($method);
            $this->type->addField($name)->type($type);
        }
    }

    protected function getTypeFromDoc($reflected) {
        if (empty($reflected->getDocComment())) {
            return false;
        }

        if ($type = $this->getCraftQlReturnType($reflected)) {
            return $type;
        }

        if ($type = $this->getPhpReturnType($reflected)) {
            return $type;
        }

        return false;
    }

    protected function getCraftQlReturnType($reflected) {
        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $docblock = $factory->create($reflected->getDocComment());
        if (!$docblock->hasTag('craftql-return')) {
            return false;
        }

        $returnType = $docblock->getTagsByName('craftql-return')[0]->getDescription()->render();
        $typeResolver = new TypeResolver();
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createFromReflector($reflected);
        /** @var Object_ $type */
        $type = $typeResolver->resolve($returnType, $context);
        var_dump((string)$type);
        die;
        return (string)$type;
    }

    protected function getPhpReturnType($reflected) {
        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $docblock = $factory->create($reflected->getDocComment());
        if (!$docblock->hasTag('return')) {
            return false;
        }

        /** @var Return_ $returnType */
        $returnType = $docblock->getTagsByName('return')[0];
        return (string)$returnType->getType();
    }

}