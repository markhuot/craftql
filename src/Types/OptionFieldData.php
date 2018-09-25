<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class OptionFieldData extends Schema {

    function boot() {
        $this->addField('selected')
            ->type(OptionFieldDataOptions::class);
        $this->addField('options')
            ->type(OptionFieldDataOptions::class)
            ->lists();
    }

}