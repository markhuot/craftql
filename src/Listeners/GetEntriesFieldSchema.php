<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Types\Entry;
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
        $schema = $event->schema;

        $schema->addField($field)
            ->type(Entry::interface($schema->getRequest()))
            //->arguments()
            //->resolve(request->entries)
            ->lists();

        $schema->addField($field)
            ->type(EntryConnection::singleton($schema->getRequest()))
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
    }
}
