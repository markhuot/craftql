<?php

namespace markhuot\CraftQL\Types;

use Craft;
use craft\web\twig\variables\Paginate;

class EntryEdge extends Edge {

    /**
     * @return EntryInterface
     */
    function getNode() {
        return $this->node;
    }

    /**
     * @return EntryDraftConnection
     */
    function getDrafts() {
        $drafts = Craft::$app->entryRevisions->getDraftsByEntryId($this->node->id);
        $pageinate = new Paginate();
        $pageinate->first = 1;
        $pageinate->last = count($drafts);
        $pageinate->total = count($drafts);
        $pageinate->totalPages = 1;
        $pageInfo = new PageInfo($pageinate, 100);
        return new EntryDraftConnection($pageInfo, $drafts);
    }

}