<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use GraphQL\Type\Definition\EnumType;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Helpers\StringHelper;

class EntryType extends BaseFactory {

    function make($raw, $request) {
        return new Entry($request, $raw);
    }

    function can($id, $mode='query') {
        return $this->request->token()->can("{$mode}:entryType:{$id}");
    }

    function getEnumKey($object) {
        return StringHelper::graphQLNameForEntryType($object);
    }

}