<?php

namespace markhuot\CraftQL;

use markhuot\CraftQL\Builders\InferredSchema;
use markhuot\CraftQL\Builders\Schema;

class TypeRegistry {

    protected $namespaces = [
        '__root__' => [],
    ];
    protected $cache = [];

    protected $dynamicTypes = [];

    /**
     * @var Request
     */
    protected $request;

    function __construct(Request $request) {
        $this->request = $request;
    }

    function registerNamespace($namespace, $prefix='__root__') {
        $this->namespaces[$prefix][] = $namespace;
    }

    function add($name, $class, $context=null) {
        $this->dynamicTypes[$name] = [$class, $context];
    }

    function getClassForName($name) {
        foreach ($this->namespaces as $prefix => $namespaces) {
            // if name starts with prefix...
            foreach ($namespaces as $namespace) {
                $fqen = "{$namespace}\\{$name}";
                if (class_exists($fqen) || trait_exists($fqen)) {
                    return $fqen;
                }
            }
        }

        if (isset($this->dynamicTypes[$name])) {
            list($class, $context) = $this->dynamicTypes[$name];
            return $class;
        }

        return false;
    }

    function get($name) {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        foreach ($this->namespaces as $prefix => $namespaces) {
            // if name starts with prefix...
            foreach ($namespaces as $namespace) {
                $fqen = "{$namespace}\\{$name}";
                if (class_exists($fqen) || trait_exists($fqen)) {
                    /** @var Schema $obj */
                    $obj = (new InferredSchema($this->request))->parse($fqen);
                    if (method_exists($obj, 'getRawGraphQLObject')) {
                        return $this->cache[$name] = $obj->getRawGraphQLObject();
                    }
                    else {
                        return $this->cache[$name] = $obj;
                    }
                }
            }
        }

        if (isset($this->dynamicTypes[$name])) {
            list($class, $context) = $this->dynamicTypes[$name];
            $obj = (new InferredSchema($this->request, $context))->parse($class)->name($name);
            return $this->cache[$name] = $obj->getRawGraphQLObject();
            // var_dump($obj);
            // die;
            // $obj = new $class($this->request, $context);
            // return $this->cache[$name] = $obj->getRawGraphQLObject();
        }

        throw new \Exception('could not resolve type `'.$name.'`');
    }

    function getDynamicTypes() {
        $types = [];

        foreach ($this->dynamicTypes as $name => $config) {
            $types[] = $this->get($name);
        }

        return $types;
    }

}