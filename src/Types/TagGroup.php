<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;

class TagGroup extends Schema {

    function boot() {
        $this->addIntField('id');
        $this->addStringField('name');
        $this->addStringField('handle');
    }

}
