<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\GraphQLFields\Query\Connection\Edges as EdgesField;
use markhuot\CraftQL\Builders\Schema;

class CategoryConnection extends ObjectType {

    protected function fields(Request $request) {
        $schema = new Schema($request);

        $schema->addRawIntField('totalCount')
            ->nonNull();

        $schema->addRawField('pageInfo')
            ->type(PageInfo::type($request));

        $schema->addRawField('edges')
            ->lists()
            ->type(CategoryEdge::singleton($request))
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $schema->addRawField('categories')
            ->lists()
            ->type(Category::interface($request))
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });

        return $schema->config();
    }

}