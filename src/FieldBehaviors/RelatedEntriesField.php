<?php

namespace markhuot\CraftQL\FieldBehaviors;

use craft\db\Paginator;
use craft\web\twig\variables\Paginate;
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

                $paginator = new Paginator($criteria, [
                    'pageSize' => @$args['limit'] ?: 100,
                    'currentPage' => \Craft::$app->request->pageNum,
                ]);

                return [
                    'totalCount' => $paginator->getTotalResults(),
                    'pageInfo' => Paginate::create($paginator),
                    'edges' => $paginator->getPageResults(),
                ];
            });
    }

}
