<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
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