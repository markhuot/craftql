<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;

class CategoryEdge {

    /**
     * @var \craft\elements\Category
     */
    private $category;

    public $cursor = 'Not implemented';

    function __construct($category) {
        $this->category = $category;
    }

    /**
     * @craftql-return CategoryInterface
     * @return \craft\elements\Category
     */
    function getNode() {
        return $this->category;
    }

    function boot() {
        // $this->addStringField('cursor');
        //
        // $this->addField('node')
        //     ->type(CategoryInterface::class)
        //     ->resolve(function ($root) {
        //         return $root['node'];
        //     });

        $this->use(new RelatedEntriesField);

        $this->addField('children')
            ->type(CategoryConnection::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria(CategoryInterface::criteriaResolver($root, $args, $context, $info, $root['node']->getChildren()));
                $pageInfo->limit = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $categories,
                ];
            });
    }

}