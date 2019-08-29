<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Tag extends Schema {

    protected $interfaces = [
        TagInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

    function getName(): string {
        return ucfirst($this->context->handle).'Tags';
    }

}
