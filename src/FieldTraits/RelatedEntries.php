<?php

namespace markhuot\CraftQL\FieldTraits;

use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\EntryConnection;
use markhuot\CraftQL\Types\PageInfo;
use markhuot\CraftQL\Types\Query;

trait RelatedEntries {

    /**
     * @return EntryConnection
     */
    function getCraftQLRelatedEntries(Request $request, $root, array $args, $context, ResolveInfo $info) {
        $criteria = Query::getEntriesCriteria($args);

        if (empty($criteria->relatedTo)) {
            $criteria->relatedTo($this->getNode());
        }

        list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
        $pageInfo = new PageInfo($pageInfo, @$args['limit'] ?: 100);
        return new EntryConnection($pageInfo, $entries);
    }

}