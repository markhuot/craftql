<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class ImagerPlaceholder extends Schema {

    function boot() {
        $this->addStringField('url');
    }

}