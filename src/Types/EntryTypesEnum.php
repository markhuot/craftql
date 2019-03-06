<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\CraftQL;

class EntryTypesEnum extends EnumObject {

    function getValues() {
        return array_map(function ($entryType) {
            return $entryType['craftQlTypeName'];
        }, CraftQL::$plugin->entryTypes->all());
    }

}