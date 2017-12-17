<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class Globals extends Schema {

    protected function getName():string {
        return ucfirst($this->context->handle);
    }

    protected function boot() {
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    }

}