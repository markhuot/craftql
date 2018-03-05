<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Types\User;

class GetUsersFieldSchema
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
            ->type(User::class)
            ->lists();

        $event->mutation->addIntArgument($field)
            ->lists();
    }
}
