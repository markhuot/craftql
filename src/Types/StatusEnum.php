<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;

class StatusEnum extends EnumObject {

    function getValues() {
        return [
            'active',
            'suspended',
            'pending',
            'locked',
        ];
    }

}