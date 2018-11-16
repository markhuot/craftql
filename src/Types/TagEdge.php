<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;
use markhuot\CraftQL\FieldTraits\RelatedEntries;

class TagEdge extends Edge {

    /**
     * @return TagInterface
     */
    function getNode() {
        return $this->node;
    }

}