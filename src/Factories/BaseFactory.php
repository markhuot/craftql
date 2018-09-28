<?php

namespace markhuot\CraftQL\Factories;

use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Request;

abstract class BaseFactory {

    /** @var string */
    private $enumName;

    /** @var array */
    static $enums = [];

    protected $repository;

    /** @var Request */
    protected $request;
    private $objects = [];

    function __construct($repository, $request) {
        $this->repository = $repository;
        $this->request = $request;
    }

    // function repository() {
    //     return $this->repository;
    // }

    function get($id, $mode='query') {
//        \Yii::beginProfile('GET::'.static::class.'::'.$id, 'GET::'.static::class.'::'.$id);
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        if ($this->can($id, $mode) === false) {
            $this->objects[$id] = false;
            return false;
        }

        $foo = $this->objects[$id] = $this->make($this->repository->get($id), $this->request);
//        \Yii::endProfile('GET::'.static::class.'::'.$id, 'GET::'.static::class.'::'.$id);
        return $foo;
    }

    abstract function can($id, $mode='query');
    abstract function make($raw, $request);

    function all($mode='query') {
        $cacheKey = static::class.'all'.$mode;
        $cache = $this->request->getCache($cacheKey);
        if ($cache !== null) {
            return $cache;
        }

        $objects = [];

        foreach ($this->repository->all() as $raw) {
            if ($object = $this->get($raw->id, $mode)) {
                $objects[] = $object;
            }
        }

        $this->request->setCache($cacheKey, $objects);

        return $objects;
    }

    function count() {
        return count($this->repository->all());
    }

    function getEnumKey($object) {
        $rawObject = $this->repository->get($object->getContext()->id);
        return $rawObject->handle;
    }

    function enum() {
        if (empty($this->enumName)) {
            $reflect = new \ReflectionClass($this);
            $this->enumName = $reflect->getShortName().'sEnum';
        }

        if (!empty(static::$enums[$this->enumName])) {
            return static::$enums[$this->enumName];
        }

        $values = [];

        foreach ($this->all() as $index => $object) {
            $values[$this->getEnumKey($object)] = $object->getContext()->id;
        }

        // Enums can't be emtpy so fake it. Craft can expose the Category or
        // Tag top level fields without any category or tag groups defined.
        if (empty($values)) {
            $values['empty'] = 'Empty';
        }

        return static::$enums[$this->enumName] = new EnumType([
            'name' => $reflect->getShortName().'sEnum',
            'values' => $values,
        ]);
    }

}