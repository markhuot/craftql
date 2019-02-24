<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class ImagerPosterizeEffect extends InputSchema {

    function boot() {
        $this->addFloatArgument('levels');
        $this->addEnumArgument('dither')->values(['no', 'riemersma', 'floydsteinberg']);
    }

}