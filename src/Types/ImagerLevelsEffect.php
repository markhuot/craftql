<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class ImagerLevelsEffect extends InputSchema {

    function boot() {
        $this->addFloatArgument('blackPoint');
        $this->addFloatArgument('gamma');
        $this->addFloatArgument('whitePoint');
        $this->addEnumArgument('channel', 'Imager')->values(['red', 'green', 'blue']);
    }

}