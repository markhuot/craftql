<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class EntryConnection extends Schema {

    function boot() {
        $this->addIntField('totalCount')
            ->nonNull();

        $this->addField('pageInfo')
            ->type(PageInfo::class);

        $this->addField('edges')
            ->lists()
            ->type(EntryEdge::class)
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $this->addField('entries')
            ->lists()
            ->type(EntryInterface::class)
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });
    }

}
