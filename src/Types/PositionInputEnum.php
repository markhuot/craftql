<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;

class PositionInputEnum extends EnumObject {

    function getValues() {
        return [
            'topLeft' => 'Top Left',
            'topCenter' => 'Top Center',
            'topRight' => 'Top Right',
            'centerLeft' => 'Center Left',
            'centerCenter' => 'Center Center',
            'centerRight' => 'Center Right',
            'bottomLeft' => 'Bottom Left',
            'bottomCenter' => 'Bottom Center',
            'bottomRight' => 'Bottom Right',
        ];
    }

}