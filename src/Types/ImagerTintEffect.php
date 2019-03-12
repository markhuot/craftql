<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class ImagerTintEffect extends InputSchema {

    function boot() {
        $this->addStringArgument('color');
    }

}