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
        if ($event->preventDefault) {
            return;
        }

        $event->schema->addStringField($event->sender);
        $event->query->addStringArgument($event->sender);
        $event->mutation->addStringArgument($event->sender);
    }
}
