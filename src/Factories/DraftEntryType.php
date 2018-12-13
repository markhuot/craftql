<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Types\EntryTypeDraft as EntryTypeDraftObjectType;

class DraftEntryType extends BaseFactory {

    function make($raw, $request) {
        return new EntryTypeDraftObjectType($raw, $request);
    }

    function can($id, $mode='query') {
        return $this->request->token()->can("{$mode}:entrytype:{$id}" || $this->request->token()->can("{$mode}:entrytype:{$id}:all"));
    }

    function getEnumName($object) {
        $rawObject = $this->repository->get($object->config['id']);
        return \markhuot\CraftQL\Types\EntryType::getName($rawObject);
    }

}