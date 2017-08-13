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

    function getEnumName($object) {
        $rawObject = $this->repository->get($object->config['id']);
        return \markhuot\CraftQL\Types\EntryType::getName($rawObject);
    }

}