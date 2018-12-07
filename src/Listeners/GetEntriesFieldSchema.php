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

        $request = $event->schema->request;
        $field = $event->sender;

        $event->schema->addField($field)
            ->type(EntryInterface::class)
            ->lists();

        $event->schema->addField($field)
            ->type(EntryConnection::class)
            ->name("{$field->handle}Connection");

        $event->mutation->addIntArgument($field)
            ->lists();
    }

    function resolve($request, $source, $args, $context, $info) {

    }
}
