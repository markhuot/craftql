<?php

namespace markhuot\CraftQL\Listeners;

class GetDefaultFieldSchema
{

    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $event->schema->addStringField($event->sender);
        // $event->schema->addStringArgument($event->field);
    }
}
