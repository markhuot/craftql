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

    function get($id) {
        if (isset($this->objects[$id])) {
            return $this->objects[$id];
        }

        if ($this->can($id) === false) {
            return false;
        }

        return $this->objects[$id] = $this->make($this->repository->get($id), $this->request);
    }

    abstract function can($id);
    abstract function make($raw, $request);

    function all() {
        $objects = [];

        foreach ($this->repository->all() as $raw) {
            if ($object = $this->get($raw->id)) {
                $objects[] = $object;
            }
        }

        return $objects;
    }

    function enumValueName($object) {
        return $object->name;
    }

    function enum() {
        if (!empty($this->enum)) {
            return $this->enum;
        }

        $values = [];

        foreach ($this->all() as $index => $object) {
            $values[$this->enumValueName($object)] = @$object->config['id'];
        }

        $reflect = new \ReflectionClass($this);
        return $this->enum = new EnumType([
            'name' => $reflect->getShortName().'Enum',
            'values' => $values,
        ]);
    }

}