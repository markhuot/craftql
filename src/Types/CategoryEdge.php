<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use craft\db\Paginator;
use craft\web\twig\variables\Paginate;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;

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
                $paginator = new Paginator(CategoryInterface::criteriaResolver($root, $args, $context, $info, $root['node']->getChildren()), [
                    'pageSize' => @$args['limit'] ?: 100,
                    'currentPage' => \Craft::$app->request->pageNum,
                ]);

                return [
                    'totalCount' => $paginator->getTotalResults(),
                    'pageInfo' => Paginate::create($paginator),
                    'edges' => $paginator->getPageResults(),
                ];
            });
    }

}
