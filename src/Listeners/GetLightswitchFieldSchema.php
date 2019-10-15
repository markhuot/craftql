<?php

namespace markhuot\CraftQL\Listeners;

class GetLightswitchFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $event->schema->addBooleanField($event->sender);
        $event->query->addBooleanArgument($event->sender);
        $event->mutation->addBooleanArgument($event->sender);
    }
}
