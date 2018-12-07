<?php

namespace markhuot\CraftQL\Builders;

use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;

class InferredBase {

    protected $request;
    protected $context;
    protected $reflectedClass;
    protected $reflectionContext;
    protected $reflectionFactory;

    function __construct($request, $context=null) {
        $this->request = $request;
        $this->context = $context;
    }

    function parse($class) {
        $this->reflectedClass = new \ReflectionClass($class);
        $contextFactory = new ContextFactory();
        $this->reflectionContext = $contextFactory->createFromReflector($this->reflectedClass);
        $this->reflectionFactory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
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
        $docblock = $this->reflectionFactory->create($reflected->getDocComment(), $this->reflectionContext);
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
        $docblock = $this->reflectionFactory->create($reflected->getDocComment(), $this->reflectionContext);
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