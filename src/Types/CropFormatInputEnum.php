<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;

class CropFormatInputEnum extends EnumObject {

    function getValues() {
        return [
            'jpg' => 'JPG',
            'gif' => 'GIF',
            'png' => 'PNG',
            'Auto' => 'Auto',
        ];
    }

}