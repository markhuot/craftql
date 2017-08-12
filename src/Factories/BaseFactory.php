<?php

namespace markhuot\CraftQL\Factories;

use GraphQL\Type\Definition\EnumType;

abstract class BaseFactory {

    private $repository;
    private $request;
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

        if ($this->request->token()->can("query:entryType:{$id}") == false) {
            return false;
        }

        return $this->objects[$id] = $this->make($this->repository->get($id), $this->request);
    }

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

    function enum() {
        if (!empty($this->enum)) {
            return $this->enum;
        }

        $values = [];

        foreach ($this->all() as $index => $object) {
            $values[$object->name] = $index;
        }

        return $this->enum = new EnumType([
            'name' => 'EntryTypeEnum',
            'values' => $values,
        ]);
    }

}