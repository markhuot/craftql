<?php

namespace markhuot\CraftQL\FieldBehaviors;

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

                list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria($criteria);
                $pageInfo->limit = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $categories,
                ];
            });
    }

}