<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\Builders\Schema;

class TagConnection extends Schema {

    function boot() {
        $this->addIntField('totalCount')
            ->nonNull();

        $this->addField('pageInfo')
            ->type(PageInfo::class);

        $this->addField('edges')
            ->lists()
            ->type(TagEdge::class)
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $this->addField('tags')
            ->lists()
            ->type(TagInterface::class)
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });
    }

}
