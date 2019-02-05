<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\SchemaBehavior;
use markhuot\CraftQL\Types\EntryConnection;

class RelatedEntriesField extends SchemaBehavior {

    function initRelatedEntriesField() {
        $this->owner->addField('relatedEntries')
            ->type(EntryConnection::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                $criteria = $this->owner->getRequest()->entries(\craft\elements\Entry::find(), $root['node'], $args, $context, $info);

                if (empty($criteria->relatedTo)) {
                    $criteria->relatedTo(@$root['node']->id);
                }

                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);
                $pageInfo->limit = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                ];
            });
    }

}