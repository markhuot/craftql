<?php

namespace markhuot\CraftQL\Factories;

use GraphQL\Type\Definition\EnumType;

abstract class BaseFactory {

    protected $repository;
    protected $request;
    private $objects = [];
    private $enum;

    function __construct($repository, $request) {
        $this->repository = $repository;
        $this->request = $request;
    }

    // function repository() {
    //     return $this->repository;
    // }

    function get($id, $mode='query') {
        if ($this->can($id, $mode) === false) {
            return false;
        }

        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        return $this->objects[$id] = $this->make($this->repository->get($id), $this->request);
    }

    abstract function can($id, $mode='query');
    abstract function make($raw, $request);

    function all($mode='query') {
        $objects = [];
        
        foreach ($this->repository->all() as $raw) {
            if ($object = $this->get($raw->id, $mode)) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    function getEnumName($object) {
        $rawObject = $this->repository->get($object->config['id']);
        return $rawObject->handle;
    }

    function enum() {
        if (!empty($this->enum)) {
            return $this->enum;
        }

        $values = [];

        foreach ($this->all() as $index => $object) {
            $values[$this->getEnumName($object)] = $object->config['id'];
        }

        $reflect = new \ReflectionClass($this);
        return $this->enum = new EnumType([
            'name' => $reflect->getShortName().'sEnum',
            'values' => $values,
        ]);
    }

}