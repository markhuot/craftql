<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class CategoryInterface extends Schema {

    function boot() {
        $this->addRawIntField('id')->nonNull();
        $this->addRawStringField('title')->nonNull();
        $this->addRawStringField('slug');
        $this->addRawStringField('uri');
        $this->addRawStringField('group')->type(CategoryGroup::class);
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    function getResolveType() {
        return function ($category) {
            return ucfirst($category->group->handle).'Category';
        };
    }

}