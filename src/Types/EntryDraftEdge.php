<?php

namespace markhuot\CraftQL\Types;

use craft\models\EntryDraft;

class EntryDraftEdge extends Edge {

    /**
     * @var EntryDraftInfo
     */
    protected $draftInfo;

    /**
     * EntryDraftEdge constructor.
     *
     * @param EntryInterface $entry
     */
    function __construct($entry) {
        parent::__construct($entry);
        $this->draftInfo = new EntryDraftInfo($this->node);
    }

    /**
     * @return EntryInterface
     */
    function getNode() {
        return $this->node;
    }

    /**
     * @return EntryDraftInfo
     */
    function getDraftInfo() {
        return $this->draftInfo;
    }

}