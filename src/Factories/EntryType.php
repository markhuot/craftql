<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Types\EntryType as EntryTypeObjectType;

class EntryType extends BaseFactory {

    function make($raw, $request) {
        return new EntryTypeObjectType($raw, $request);
    }

    function can($id, $mode='query') {
        return $this->request->token()->can("{$mode}:entryType:{$id}");
    }

    function enum() {
        if (!empty($this->enum)) {
            return $this->enum;
        }

        $values = [];

        foreach ($this->all() as $index => $object) {
            $rawObject = $this->repository->get($object->config['id']);
            $values[\markhuot\CraftQL\Types\EntryType::getName($rawObject)] = $rawObject->id;
        }

        $reflect = new \ReflectionClass($this);
        return $this->enum = new EnumType([
            'name' => $reflect->getShortName().'sEnum',
            'values' => $values,
        ]);
    }

}