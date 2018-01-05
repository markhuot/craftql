<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;
use markhuot\CraftQL\Types\CategoryInterface;

class GetCategoriesFieldSchema
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

        if (!$event->schema->getRequest()->token()->can('query:categories')) {
            return;
        }

        if (preg_match('/^group:(\d+)$/', $field->source, $matches)) {
            $groupId = $matches[1];

            $event->schema->addField($field)
                ->lists()
                ->type($event->schema->getRequest()->categoryGroups()->get($groupId))
                ->resolve(function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                });

            $event->query->addStringArgument($field);

            $event->mutation->addIntArgument($field)
                ->lists();
        }

    }
}
