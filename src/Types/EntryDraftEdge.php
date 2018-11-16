<?php

namespace markhuot\CraftQL\Types;

class EntryDraftEdge extends Edge {

    /**
     * EntryDraftEdge constructor.
     *
     * @param \craft\elements\Entry $draft
     */
    function __construct(\craft\elements\Entry $draft) {
        $this->node = $draft;
        $this->draftInfo = new EntryDraftInfo($draft);
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