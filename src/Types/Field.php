<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Field {

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @var string
     */
    public $fieldType;

    /**
     * @var string
     */
    public $settings;

    // function boot() {
    //     $this->addStringField('settings')
    //         ->resolve(function ($root, $args) {
    //             return json_encode($root['settings']);
    //         });
    // }

}