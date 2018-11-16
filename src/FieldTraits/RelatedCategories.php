<?php

namespace markhuot\CraftQL\FieldTraits;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\CategoryConnection;
use markhuot\CraftQL\Types\EntryConnection;
use markhuot\CraftQL\Types\PageInfo;
use markhuot\CraftQL\Types\Query;

trait RelatedCategories {

    /**
     * @return CategoryConnection
     */
    function getCraftQLRelatedCategories(Request $request, $root, array $args, $context, ResolveInfo $info) {
        $criteria = Query::getCategoryCriteria($args);

        if (empty($criteria->relatedTo)) {
            $criteria->relatedTo($this->getNode());
        }

        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
        $pageInfo = new PageInfo($pageInfo, @$args['limit'] ?: 100);
        return new CategoryConnection($pageInfo, $entries);
    }

}