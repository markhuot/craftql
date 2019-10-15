<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;

class GetVideosFieldSchema
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

        $object = $schema->createObjectType('Videos');
        $object->addStringField('title');
        $object->addStringField('url');
        $embed = $object->addStringField('embed')
            ->resolve(function ($root, $args) {
                return (string)$root->getEmbed($args);
            });

        $embed->addIntArgument('width');
        $embed->addIntArgument('height');

        $schema->addStringField($field)
            ->type($object)
            ->resolve(function ($root, $args) use ($field) {
                return $root->{$field->handle};
            });
    }
}
