<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Tag as TagObjectType;

class TagGroup extends BaseFactory {

    function make($raw, $request) {
        return new TagObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        return true;
    }

}