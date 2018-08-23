<?php

namespace markhuot\CraftQL\Listeners;

use GraphQL\Type\Definition\Type;

class GetColorFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $event->schema->addStringField($event->sender)->resolve(function ($root) {
            return (string)$root->color->hex;
        });

        // @TODO handle Mutations?
    }
}
