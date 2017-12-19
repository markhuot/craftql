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
        $schema = $event->schema;

        if ($field->decimals == 0) {
            $schema->addIntField($field);
        }
        else {
            $schema->addFloatField($field);
        }
    }
}
