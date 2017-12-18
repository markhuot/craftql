<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class PageInfo extends Schema {

    function boot() {
        $this->addRawBooleanField('hasPreviousPage')
            ->nonNull()
            ->resolve(function ($root, $args) {
                return $root->currentPage > 1;
            });

        $this->addRawBooleanField('hasNextPage')
            ->nonNull()
            ->resolve( function ($root, $args) {
            return $root->currentPage < $root->totalPages;
        });

        $this->addRawIntField('currentPage');
        $this->addRawIntField('totalPages');
        $this->addRawIntField('first');
        $this->addRawIntField('last');
    }

}