<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\FieldBehaviors\RelatedCategoriesField;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;

class EntryEdge {

    /**
     * @var EntryInterface
     */
    public $node;

    public $cursor = 'Not implemented';

    function __construct($entry) {
        $this->node = $entry;
    }

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