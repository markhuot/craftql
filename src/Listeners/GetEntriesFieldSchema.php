<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Types\EntryInterface;
use markhuot\CraftQL\Types\EntryConnection;

class GetEntriesFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $field = $event->sender;

        $event->schema->addField($field)
            ->type(EntryInterface::class)
            ->lists();

        $event->schema->addField($field)
            ->type(EntryConnection::class)
            ->name("{$field->handle}Connection")
            ->resolve(function ($root, $args) use ($field) {
                $criteria = $root->{$field->handle};
                list($pageInfo, $entries) = \craft\helpers\Template::paginateCriteria($criteria);

                return [
                    'totalCount' => $pageInfo->total,
                    'pageInfo' => $pageInfo,
                    'edges' => $entries,
                    'criteria' => $criteria,
                    'args' => $args,
                ];
            });

        $event->mutation->addIntArgument($field)
            ->lists();
    }
}
