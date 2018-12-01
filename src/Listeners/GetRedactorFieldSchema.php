<?php

namespace markhuot\CraftQL\Listeners;

use craft\redactor\FieldData;
use markhuot\CraftQL\Types\RedactorFieldData;

class GetRedactorFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $event->schema->addField($event->sender)->type(RedactorFieldData::class);
        $event->query->addStringArgument($event->sender);
        $event->mutation->addStringArgument($event->sender);
    }
}
