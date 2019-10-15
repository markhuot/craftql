<?php

namespace markhuot\CraftQL\Listeners;

class GetDateFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $event->schema->addDateField($event->sender);

        $event->query->addStringArgument($event->sender);

        // @TODO make this timezone aware. if you send a unixtime stamp it should be for GMT
        $event->mutation->addIntArgument($event->sender)
//            ->onSave(function($value) {
//                var_dump($value);
//                die;
//            })
        ;
    }
}
