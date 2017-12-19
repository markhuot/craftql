<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class ElementInterface extends Schema {

    function boot() {
        $this->addStringField('elementType');
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    function getResolveType() {
        return function ($element) {
            switch ($element->elementType) {
                // @TODO add Tag elements
                case 'Category':
                    return ucfirst($element->group->handle);
                    break;
                case 'Entry':
                    return ucfirst($element->section->handle);
                    break;
            }
        };
    }

}