<?php

namespace markhuot\CraftQL\FieldBehaviors;

use craft\db\Paginator;
use craft\web\twig\variables\Paginate;
use markhuot\CraftQL\Behaviors\SchemaBehavior;
use markhuot\CraftQL\Types\CategoryConnection;
use markhuot\CraftQL\Types\EntryConnection;

class RelatedCategoriesField extends SchemaBehavior {

    function initRelatedCategoriesField() {
        if ($this->owner->getRequest()->categoryGroups()->count() == 0) {
            return;
        }

        $this->owner->addField('relatedCategories')
            ->type(CategoryConnection::class)
            ->use(new CategoryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = \craft\elements\Category::find();

                if (empty($criteria->relatedTo)) {
                    $criteria->relatedTo(@$root['node']->id);
                }

                $paginator = new Paginator($criteria, [
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
