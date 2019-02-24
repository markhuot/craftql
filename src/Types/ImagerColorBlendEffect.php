<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class ImagerColorBlendEffect extends InputSchema {

    function boot() {
        $this->addStringArgument('color');
        $this->addFloatArgument('opacity');
    }

}