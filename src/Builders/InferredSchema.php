<?php

namespace markhuot\CraftQL\Builders;

use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\ProxyObject;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\ContextFactory;
use phpDocumentor\Reflection\Types\Object_;

class InferredSchema extends InferredBase {

    /** @var BaseBuilder */
    private $type;

    function parse($class) {
        parent::parse($class);

        $cacheKey = $class;
        if (!empty($this->context->id)) {
            $cacheKey = "{$cacheKey}::{$this->context->id}";
        }
        if ($this->request->hasType($cacheKey)) {
            return $this->request->getType($cacheKey);
        }

        $reflect = $this->reflectedClass;

        $type = ObjectType::class;
        $doc = $reflect->getDocComment();
        if (preg_match('/@craftql-type interface/', $doc)) {
            $type = InterfaceType::class;
        }
        else if (preg_match('/@craftql-type enum/', $doc)) {
            $type = EnumType::class;
        }
        else if (preg_match('/@craftql-type input/', $doc)) {
            $type = InputObjectType::class;
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
                // @TODO this is gross, needs to be cleaned up
                if (is_subclass_of($source, ProxyObject::class)) {
                    $source = $source->getSource();
                }
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

        // get enum constants
        $this->parseConstants($reflect->getReflectionConstants());

        // get object fields
        $this->type->addFieldClosure(function () use ($class, $reflect) {
            $this->parseProperties($reflect->getProperties());
            $this->parseMethods($reflect->getMethods());

            if ($this->context) {
                if (!empty($this->context->fieldLayoutId)) {
                    $this->type->addFieldsByLayoutId($this->context->fieldLayoutId);
                }
            }

            if (method_exists($class, 'craftQlFields')) {
                $class::craftQlFields($this->type, $this->request);
            }
        });

        $this->request->addType($cacheKey, $this->type);
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

        $field = $this->type->addField($property->getName())->type($type)->lists($isList);

        if ($this->getNonNullFromdoc($property)) {
            $field->nonNull();
        }
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
        // skip any method that does not follow the `getFieldName()` pattern
        if (!preg_match('/^get([A-Z][a-zA-Z0-9_]*)$/', $method->getName(), $matches)) {
            return;
        }

        // skip any non-public methods
        if (!$method->isPublic()) {
            return;
        }

        // skip static methods
        if ($method->isStatic()) {
            return;
        }

        $name = preg_replace('/^CraftQL/', '', $matches[1]);
        $name = lcfirst($name);
        list($type, $isList) = $this->getTypeFromDoc($method);

        /** @var Field $field */
        $field = $this->type->addField($name)->type($type)->lists($isList);

        if ($method->getName() == 'getCraftQLEntry') {
            /** @var \ReflectionParameter $param */
            $param = $method->getParameters()[2];

            /** @var \ReflectionNamedType $type */
            $type = $param->getType();

            if ($type) {
                $arguments = (new InferredArguments($this->request))->parse($type->getName());
                $field->addArguments($arguments);
            }
        }
    }

    /**
     * @param $reflected
     * @return bool
     */
    function getNonNullFromdoc($reflected) {
        return strpos($reflected->getDocComment(), '@craftql-nonNull') !== false;
    }

    /**
     *
     */
    function parseConstants($constants) {
        foreach ($constants as $constant) {
            $this->parseConstant($constant);
        }
    }

    function parseConstant(\ReflectionClassConstant $reflected) {
        // @TODO only add ENUM "values" if the type is an actual ENUM. There's a chance that regular objects could have CONST attributes too
        $this->type->addValue($reflected->getName(), $reflected->getValue());
    }

}