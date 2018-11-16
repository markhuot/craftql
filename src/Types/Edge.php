<?php

namespace markhuot\CraftQL\Types;

use craft\base\Element;
use markhuot\CraftQL\FieldTraits\RelatedCategories;
use markhuot\CraftQL\FieldTraits\RelatedEntries;

abstract class Edge {

    use RelatedEntries;
    use RelatedCategories;

    /**
     * @var Element
     */
    protected $node;

    public $cursor = 'Not implemented';

    function __construct($entry) {
        $this->node = $entry;
    }

    abstract function getNode();

    /**
     * @return EntryDraftConnection
     */
    function getDrafts() {
        $drafts = Craft::$app->entryRevisions->getDraftsByEntryId($this->node->id);
        return new EntryDraftConnection($drafts);
    }

    // function boot() {
    //     $this->use(new RelatedEntriesField);
    //     $this->use(new RelatedCategoriesField);
    // }

}