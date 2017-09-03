<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\TagGroup as TagGroupObjectType;

class TagGroup extends BaseFactory {

    function make($raw, $request) {
        return new TagGroupObjectType($raw, $request);
    }

    function can($id, $mode='query') {
        return true;
    }

}