<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Types\Globals as GlobalsObjectType;

class Globals extends BaseFactory {

    function make($raw, $request) {
        return new GlobalsObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        return true;
    }

}
