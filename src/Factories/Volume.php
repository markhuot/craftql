<?php

namespace markhuot\CraftQL\Factories;

use markhuot\CraftQL\Factories\BaseFactory;
use markhuot\CraftQL\Types\Volume as VolumeObjectType;

class Volume extends BaseFactory {

    function make($raw, $request) {
        return new VolumeObjectType($raw, $request);
    }

    function can($id) {
        return true;
    }

}