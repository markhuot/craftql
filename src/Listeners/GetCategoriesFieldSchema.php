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
        $schema = $event->schema;

        if (!$schema->getRequest()->token()->can('query:categories')) {
            return;
        }

        if (preg_match('/^group:(\d+)$/', $field->source, $matches)) {
            $groupId = $matches[1];

            $schema->addField($field)
                ->lists()
                ->type($schema->getRequest()->categoryGroups()->get($groupId))
                ->resolve(function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                });
        }
    }
}
