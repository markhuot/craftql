<?php

namespace markhuot\CraftQL\Types;

use Craft;
use markhuot\CraftQL\FieldBehaviors\RelatedCategoriesField;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;

class EntryEdge extends Schema {

    function boot() {
        $this->addStringField('cursor');

        $this->addField('node')
            ->type(EntryInterface::class)
            ->resolve(function ($root) {
                return $root['node'];
            });

        $this->use(new RelatedEntriesField);
        $this->use(new RelatedCategoriesField);

        $this->addField('drafts')
            ->type(EntryDraftConnection::class)
            ->resolve(function ($root, $args, $context, $info) {
                /** @var \craft\elements\Entry $entry */
                $entry = $root['node'];
                $drafts = Craft::$app->entryRevisions->getDraftsByEntryId($entry->id, $entry->siteId, true);
                return [
                    'totalCount' => count($drafts),
                    'pageInfo' => [
                        'currentPage' => 1,
                        'totalPages' => 1,
                        'first' => 1,
                        'last' => 1,
                    ],
                    'edges' => $drafts,
                ];
            });
    }

}
