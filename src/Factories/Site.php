<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Site as SiteObjectType;

class Site extends BaseFactory {

    function make($raw, $request) {
        return new SiteObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        return true;
    }

}