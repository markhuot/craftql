<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Field extends Schema {

    function boot() {
        $this->addStringField('name');
        $this->addStringField('handle');
        $this->addStringField('fieldType')
            ->resolve(function ($root, $args) {
                return get_class($root);
            });
        $this->addStringField('settings')
            ->resolve(function ($root, $args) {
                return json_encode($root['settings']);
            });
    }

}
