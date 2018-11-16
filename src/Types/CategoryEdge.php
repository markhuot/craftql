<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;
use markhuot\CraftQL\FieldTraits\RelatedEntries;

class CategoryEdge extends Edge {

    /**
     * @return CategoryInterface
     */
    function getNode() {
        return $this->node;
    }

    /**
     * @return CategoryConnection
     */
    function getChildren() {
        list($pageInfo, $categories) = \craft\helpers\Template::paginateCriteria(Query::getCategoryCriteria([], $this->node->getChildren()));
        return new CategoryConnection(new PageInfo($pageInfo), $categories);
    }

}