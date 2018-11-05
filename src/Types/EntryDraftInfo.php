<?php

namespace markhuot\CraftQL\Types;

use craft\models\EntryDraft;

class EntryDraftInfo {

    /**
     * @var EntryDraft
     */
    private $draft;

    /**
     * EntryDraftInfo constructor.
     *
     * @param EntryDraft $draft
     */
    function __construct(EntryDraft $draft) {
        $this->draft = $draft;
    }

    /**
     * @return int
     */
    function getDraftId() {
        return $this->draft->draftId;
    }

    /**
     * @return string
     */
    function getName() {
        return $this->draft->name;
    }

    /**
     * @return string
     */
    function getNotes() {
        return $this->draft->revisionNotes;
    }

}