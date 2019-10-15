<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class GlobalsSet extends Schema {

    function boot() {
        foreach ($this->request->globals()->all() as $globalSet) {
            $this->addField($globalSet->getContext()->handle)
                ->type($globalSet);
        }
    }

}
