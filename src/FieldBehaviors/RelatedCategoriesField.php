<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\SchemaBehavior;
use markhuot\CraftQL\TypeModels\PageInfo;
use markhuot\CraftQL\Types\CategoryConnection;

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
