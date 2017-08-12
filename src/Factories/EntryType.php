<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\EntryType as EntryTypeObjectType;

class EntryType extends BaseFactory {

    function make($raw, $request) {
        return new EntryTypeObjectType($raw, $request);
    }

}