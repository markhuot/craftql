<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Volume extends Schema {

    protected $interfaces = [
        \markhuot\CraftQL\Types\VolumeInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    function getName(): string {
        return ucfirst($this->context->handle).'Volume';
    }

}
