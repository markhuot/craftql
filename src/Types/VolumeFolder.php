<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class VolumeFolder extends Schema {

    function boot() {
        $this->addIntField('id');
        $this->addIntField('volumeId');
        $this->addStringField('name');
        $this->addStringField('path');
    }

}