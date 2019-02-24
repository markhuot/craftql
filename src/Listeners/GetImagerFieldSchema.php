<?php

namespace markhuot\CraftQL\Listeners;

use aelvan\imager\Imager;
use markhuot\CraftQL\Builders\Field;
use markhuot\CraftQL\Builders\InputSchema;
use markhuot\CraftQL\Types\ImagerTransformedImageModel;
use markhuot\CraftQL\Types\ImagerTransformedImages;

class GetImagerFieldSchema {

    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->preventDefault = true;

        $craftField = $event->sender;
        $schema = $event->schema;

        /** @var Field $field */
        $field = $schema->addField($craftField)
            ->lists()
            ->type(ImagerTransformedImages::class)
            ->name("{$craftField->handle}Imager")
            ->resolve(function ($root, $args, $context, $info) use ($craftField) {
                return $root->{$craftField->handle}->all();
            });

    }

}