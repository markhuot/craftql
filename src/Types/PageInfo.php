<?php

namespace markhuot\CraftQL\Types;

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
