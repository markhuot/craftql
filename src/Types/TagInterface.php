<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class TagInterface extends Schema {

    function boot() {
        $this->addRawIntField('id')->nonNull();
        $this->addRawStringField('title')->nonNull();
        $this->addRawStringField('slug')->nonNull();
        $this->addRawField('group')->nonNull()->type(TagGroup::class);
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    function getResolveType() {
        return function ($tag) {
            return ucfirst($tag->group->handle).'Tags';
        };
    }

}