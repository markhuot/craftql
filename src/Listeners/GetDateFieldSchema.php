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
    function handle($event) {
        $event->handled = true;

        $field = $event->sender;
        $schema = $event->schema;
        // $request = $event->request;

        $schema->addDateField($field);
        // $schema->addCraftArgument($field, Type::int());
    }
}
