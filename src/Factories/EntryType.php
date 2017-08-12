<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\EntryType as EntryTypeObjectType;

class EntryType extends BaseFactory {

    function make($raw, $request) {
        return new EntryTypeObjectType($raw, $request);
    }

    function can($id) {
        return $this->request->token()->can("query:entryType:{$id}");
    }

}