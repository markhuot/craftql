<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;

class Category extends Schema {

    protected $interfaces = [
        CategoryInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context['fieldLayoutId']);
    }

    function getName(): string {
        return ucfirst($this->context['handle']).'Category';
    }

}