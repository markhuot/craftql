<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class EntryInterfaceAlternate extends Schema {

    function boot() {
        $this->addField('entry')->type(EntryInterface::class);
        $this->addBooleanField('isSelf')->nonNull();
    }

}