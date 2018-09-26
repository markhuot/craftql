<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class OptionFieldDataOptions extends Schema {

    function boot() {
        $this->addStringField('value');
        $this->addStringField('label');
        $this->addBooleanField('selected');
    }

}