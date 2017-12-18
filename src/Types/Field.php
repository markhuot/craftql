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
        $this->addRawStringField('name');
        $this->addRawStringField('handle');
        $this->addRawStringField('fieldType')
            ->resolve(function ($root, $args) {
                return get_class($root);
            });
        $this->addRawStringField('settings')
            ->resolve(function ($root, $args) {
                return json_encode($root['settings']);
            });
    }

}