<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Events\GetFieldSchema;

class GetSuperTableFieldSchema
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

        $blockType = current($field->getBlockTypes());
        $craftQlBlockType = $event->schema->createObjectType(ucfirst($field->handle).'SuperTableBlockType');
        $craftQlBlockType->addIntField('id');
        $craftQlBlockType->addFieldsByLayoutId($blockType->fieldLayoutId);

        $event->schema->addStringField($field)
            ->type($craftQlBlockType)
            ->lists()
            ->resolve(function ($root, $args) use ($field) {
                return $root->{$field->handle}->all();
            });
    }
}
