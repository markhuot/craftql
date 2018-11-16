<?php

namespace markhuot\CraftQL\Builders;

use GraphQL\Type\Definition\ScalarType;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Types\EntryInterface;
use markhuot\CraftQL\Types\Timestamp;
use markhuot\CraftQL\Types\VolumeInterface;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;

class InferredSchema {

    /** @var BaseBuilder */
    private $type;

    /** @var Request */
    private $request;

    /**
     * The context of this inference, if any. This is most commonly used
     * during the field generation to add custom fields to the GraphQL type
     *
     * @var mixed
     */
    private $context;

    function __construct($request, $context=null) {
        $this->request = $request;
        $this->context = $context;
    }

    function parse($class) {
        if ($this->request->hasType($class)) {
            return $this->request->getType($class);
        }

        $reflect = new \ReflectionClass($class);

        $type = Schema::class;
        $doc = $reflect->getDocComment();
        if (preg_match('/@craftql-type interface/', $doc)) {
            $type = InterfaceBuilder::class;
        }

        $this->type = new $type($this->request);
        $this->type->name($reflect->getShortName());

        // This is for uncached schemas, it is not used when the
        // schema is cached because the closure is lost.
        if ($type == InterfaceBuilder::class) {
            // var_dump($class);
            $this->type->resolveType(function($source) use ($class) {
                // var_dump($class, $source);
                // die;
                return $class::craftQLResolveType($source);
            });
        }

        foreach ($reflect->getTraits() as $trait) {
            if (preg_match('/@craftql-type interface/', $trait->getDocComment())) {
                /** @var Schema $type */
                $type = $this->type;
                $type->interface($trait->getName());
            }
        }

        $this->parseProperties($reflect->getProperties());
        $this->parseMethods($reflect->getMethods());

        if ($this->context) {
            if (!empty($this->context->fieldLayoutId)) {
                $this->type->addFieldsByLayoutId($this->context->fieldLayoutId);
            }
        }

        $this->request->addType($class, $this->type);
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

        list($type, $isList) = $this->getTypeFromDoc($property);

        $this->type->addField($property->getName())->type($type)->lists($isList);
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
        if (!preg_match('/^get([A-Z][a-zA-Z0-9_]*)$/', $method->getName(), $matches)) {
            return;
        }

        $name = preg_replace('/^CraftQL/', '', $matches[1]);
        $name = lcfirst($name);
        list($type, $isList) = $this->getTypeFromDoc($method);
        $this->type->addField($name)->type($type)->lists($isList);
    }

    protected function getTypeFromDoc($reflected) {
        if (empty($reflected->getDocComment())) {
            return false;
        }

        if ($type = $this->getCraftQlReturnType($reflected)) {
            return $type;
        }

        if ($type = $this->getPhpReturnType($reflected, 'return')) {
            return $type;
        }

        if ($type = $this->getPhpReturnType($reflected, 'var')) {
            return $type;
        }

        return false;
    }

    protected function getCraftQlReturnType($reflected) {
        $isList = false;
        $contextFactory = new ContextFactory;
        $context = $contextFactory->createFromReflector($reflected);
        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $docblock = $factory->create($reflected->getDocComment(), $context);
        if (!$docblock->hasTag('craftql-return')) {
            return false;
        }

        $returnType = $docblock->getTagsByName('craftql-return')[0]->getDescription()->render();
        $typeResolver = new TypeResolver();
        $contextFactory = new ContextFactory();
        $context = $contextFactory->createFromReflector($reflected);
        /** @var Object_|Array_ $type */
        $type = $typeResolver->resolve($returnType, $context);
        if (is_a($type, Array_::class)) {
            $isList = true;
            $type = $type->getValueType();
        }
        return [(string)$type, $isList];
    }

    protected function getPhpReturnType($reflected, $tag='return') {
        $isList = false;
        $contextFactory = new ContextFactory;
        $context = $contextFactory->createFromReflector($reflected);
        $factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
        $docblock = $factory->create($reflected->getDocComment(), $context);
        if (!$docblock->hasTag($tag)) {
            return false;
        }

        /** @var Return_ $returnType */
        $returnType = $docblock->getTagsByName($tag)[0];
        $type = $returnType->getType();
        if (is_a($type, Array_::class)) {
            $isList = true;
            $type = $type->getValueType();
        }
        return [(string)$type, $isList];
    }

}