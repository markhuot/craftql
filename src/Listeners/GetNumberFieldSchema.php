<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;

class GetNumberFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle(GetFieldSchema $event) {
        $event->handled = true;

        $field = $event->sender;

        if ($field->decimals == 0) {
            $event->query->addIntField($field);
            $event->mutation->addIntArgument($field);
        }
        else {
            $event->query->addFloatField($field);
            $event->mutation->addFloatArgument($field);
        }
    }
}
