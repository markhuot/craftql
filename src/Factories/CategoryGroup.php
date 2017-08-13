<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\CategoryGroup as CategoryGroupObjectType;

class CategoryGroup extends BaseFactory {

    function make($raw, $request) {
        return new CategoryGroupObjectType($raw, $request);
    }

    function can($id, $mode='query') {
        return true;
    }

}