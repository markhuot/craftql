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
    static function handle($event) {
        $event->handled = true;

        return $event->builder
            ->addStringField($event->field)
            ->addStringMutation($event->field);
    }
}
