<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class ImagerPosterizeEffect extends InputSchema {

    function boot() {
        $this->addFloatArgument('levels');
        $this->addEnumArgument('dither', 'Imager')->values(['no', 'riemersma', 'floydsteinberg']);
    }

}