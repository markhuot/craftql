<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Globals extends Schema {

    function getName():string {
        return ucfirst($this->context->handle);
    }

    protected function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    /**
     * Get the context used to create this schema
     *
     * return \craft\elements\GlobalSet
     */
    function getContext(): \craft\elements\GlobalSet {
        return parent::getContext();
    }

}