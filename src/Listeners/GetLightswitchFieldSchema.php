<?php

namespace markhuot\CraftQL\Listeners;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class GetLightswitchFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    static function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $builder = $event->builder;

        $builder
            ->addBooleanField($field)
            ->addBooleanArgument($field);
    }
}
