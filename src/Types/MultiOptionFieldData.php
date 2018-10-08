<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class MultiOptionFieldData extends Schema {

    function boot() {
        $this->addField('selected')
            ->type(OptionFieldDataOptions::class)
            ->lists();
        $this->addField('options')
            ->type(OptionFieldDataOptions::class)
            ->lists();
    }

}