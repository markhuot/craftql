<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class ImagerTransformedImageModel extends Schema {

    function boot() {
        $this->addStringField('url');
        $this->addStringField('path');
        $this->addStringField('extension');
        $this->addStringField('mimetype');
        $this->addStringField('width');
        $this->addStringField('height');
        $this->addStringField('size');
    }

}