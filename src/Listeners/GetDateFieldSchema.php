<?php

namespace markhuot\CraftQL\Listeners;

use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class GetDateFieldSchema
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
        $request = $event->request;

        $type = (new DateField($request, !!$field->required))
            ->setDescription($field->instructions)
            ->toArray();

        $builder
            ->addField(
                $field->handle,
                $type
            )
            ->addCraftArgument(
                $field,
                Type::int()
            );
    }
}
