<?php

namespace markhuot\CraftQL;

use markhuot\CraftQL\Models\Token;

class Request {

    private $token;

    function __construct($token) {
        $this->token = $token;
    }

    /**
     * @return Token
     */
    function token() {
        return $this->token;
    }

    private function parseRelatedTo($relations, $id) {
        foreach ($relations as $index => &$relatedTo) {
            foreach (['element', 'sourceElement', 'targetElement'] as $key) {
                if (!empty($relatedTo["{$key}IsEdge"])) {
                    $relatedTo[$key] = $id;
                    unset($relatedTo["{$key}IsEdge"]);
                }
            }
        }

        return $relations;
    }

    function entries($criteria, $root, $args, $context, $info) {
        // if (empty($args['section'])) {
        //     $args['sectionId'] = array_map(function ($value) {
        //         return $value->value;
        //     }, $this->sections()->enum()->getValues());
        // }
        // else {
        //     $args['sectionId'] = $args['section'];
        //     unset($args['section']);
        // }

        // if (empty($args['type'])) {
        //     $args['typeId'] = array_map(function ($value) {
        //         return $value->value;
        //     }, $this->entryTypes()->enum()->getValues());
        // }
        // else {
        //     $args['typeId'] = $args['type'];
        //     unset($args['type']);
        // }

        if (!empty($args['relatedTo'])) {
            $criteria->relatedTo(array_merge(['and'], $this->parseRelatedTo($args['relatedTo'], @$root->id)));
            unset($args['relatedTo']);
        }

        if (!empty($args['orRelatedTo'])) {
            $criteria->relatedTo(array_merge(['or'], $this->parseRelatedTo($args['orRelatedTo'], @$root->id)));
            unset($args['orRelatedTo']);
        }

        if (!empty($args['idNot'])) {
            // this looks a little unusual to fit craft\helpers\Db::parseParam
            $criteria->id('and, !='.implode(', !=', $args['idNot']));
            unset($args['idNot']);
        }

        // var_dump($args);
        // die;

        foreach ($args as $key => $value) {
            $criteria = $criteria->{$key}($value);
        }

        if (!empty($info->fieldNodes)) {
            foreach ($info->fieldNodes[0]->selectionSet->selections as $selection) {
                if (isset($selection->name->value) && $selection->name->value == 'author') {
                    $criteria->with('author');
                }
            }
        }

        return $criteria;
    }

    private $types = [];
    static $typeCaches = [];
    private $namespaces = [];

    function registerNamespace($namespace, $prefix='') {
        $this->namespaces[] = ['namespace' => $namespace, 'prefix' => $prefix];
    }

    function registerType($name, $obj) {
        $this->types[$name] = $obj;
    }

    function getTypeBuilder($name) {
        $type = @$this->types[$name];

        if (is_callable($type)) {
            $type = $type();
        }

        return $type;
    }

    function getAllTypes() {
        return array_map(function ($typeName) {
            return $this->getType($typeName);
        }, array_keys($this->types));
    }

    function getType($name) {
        if (!empty(static::$typeCaches[$name])) {
            return static::$typeCaches[$name];
        }

        $type = @$this->types[$name];

        if (is_callable($type)) {
            $type = $type();
        }

        if (method_exists($type, 'getRawGraphQLObject')) {
            return static::$typeCaches[$name] = $type->getRawGraphQLObject();
        }

        foreach ($this->namespaces as $namespaceConfig) {
            $namespace = $namespaceConfig['namespace'];
            $prefix = $namespaceConfig['prefix'];
            $class = $namespace.'\\'.$prefix.$name;
            if (class_exists($class)) {
                return static::$typeCaches[$name] = (new $class($this))->getRawGraphQLObject();
            }
        }

        if ($type) {
            return static::$typeCaches[$name] = $type;
        }

        throw new \Exception('Could not find '.$name.' in the type registry.');
    }

}
