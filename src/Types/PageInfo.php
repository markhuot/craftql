<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class PageInfo extends Schema {

    function boot() {
        $this->addBooleanField('hasPreviousPage')
            ->nonNull()
            ->resolve(function ($root, $args) {
                return $root->currentPage > 1;
            });

        $this->addBooleanField('hasNextPage')
            ->nonNull()
            ->resolve( function ($root, $args) {
            return $root->currentPage < $root->totalPages;
        });

        $this->addIntField('currentPage');
        $this->addIntField('totalPages');
        $this->addIntField('first');
        $this->addIntField('last');
    }

}