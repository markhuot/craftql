<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Types\Volume as VolumeObjectType;

class Volume extends BaseFactory {

    function make($raw, $request) {
        return new VolumeObjectType($request, $raw);
    }

    function can($id, $mode='query') {
        return true;
    }

}
