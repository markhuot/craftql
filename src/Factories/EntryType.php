<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Types\EntryTypeDerivitive as EntryTypeObjectType;

class EntryType extends BaseFactory {

    function make($raw, $request) {
        return new EntryTypeObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        return $this->request->token()->can("{$mode}:entryType:{$id}");
    }

    function getEnumName($object) {
        return 'Stories';
        // $rawObject = $this->repository->get($object->config['id']);
        // return \markhuot\CraftQL\Types\EntryTypeDerivitive::getName($rawObject);
    }

}