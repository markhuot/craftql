<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;
use markhuot\CraftQL\TypeModels\PageInfo;

class CategoryEdge extends Schema {

    function boot() {
        $this->addStringField('cursor');

        $this->addField('node')
            ->type(CategoryInterface::class)
            ->resolve(function ($root) {
                return $root['node'];
            });

        $this->use(new RelatedEntriesField);

        $this->addField('children')
            ->type(CategoryConnection::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = CategoryInterface::criteriaResolver($root, $args, $context, $info, $root['node']->getChildren(), false);
                $totalCount = $criteria->count();
                $offset = @$args['offset'] ?: 0;
                $perPage = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $totalCount,
                    'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                    'edges' => $criteria->all(),
                ];
            });
    }

}
