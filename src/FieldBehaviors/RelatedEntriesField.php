<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\SchemaBehavior;
use markhuot\CraftQL\TypeModels\PageInfo;
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

                $totalCount = $criteria->count();
                $offset = @$args['offset'] ?: 0;
                $perPage = @$args['limit'] ?: 100;

                return [
                    'totalCount' => $totalCount,
                    'pageInfo' => new PageInfo($offset, $perPage, $totalCount),
                    'edges' => $criteria->all(),
                ];
            });
    }

}
