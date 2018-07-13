<?php

namespace markhuot\CraftQL\Types;

use craft\web\twig\variables\Paginate;
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

        $this->addIntField('currentPage')
            ->resolve(function (Paginate $root, $args) {
                // last and first are not inclusive so add one to the range
                $perPage = ceil($root->last - $root->first + 1);

                // first is 1-based so subtract one to work with 0 based indexes
                // add one at the end because Craft's pages are, by default, 1 based
                return floor(($root->first - 1) / $perPage) + 1;
            });

        $this->addIntField('totalPages');
        $this->addIntField('first');
        $this->addIntField('last');
    }

}