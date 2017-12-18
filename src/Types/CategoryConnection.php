<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\GraphQLFields\Query\Connection\Edges as EdgesField;
use markhuot\CraftQL\Builders\Schema;

class CategoryConnection extends Schema {

    function boot() {
        $this->addRawIntField('totalCount')
            ->nonNull();

        $this->addRawField('pageInfo')
            ->type(PageInfo::class);

        $this->addRawField('edges')
            ->lists()
            ->type(CategoryEdge::class)
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $this->addRawField('categories')
            ->lists()
            ->type(CategoryInterface::class)
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });
    }

}