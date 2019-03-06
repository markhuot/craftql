<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\CraftQL;

class SectionsEnum extends EnumObject {

    function getValues() {
        return array_map(function ($section) {
            return $section['handle'];
        }, CraftQL::$plugin->sections->all());
    }

}