<?php

namespace markhuot\CraftQL\Listeners;

use markhuot\CraftQL\Helpers\StringHelper;
class GetSelectMultipleFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $craftField = $event->sender;

        $graphqlField = $event->schema->addEnumField($craftField)
            ->lists()
            ->values([GetSelectOneFieldSchema::class, 'valuesForField'], $craftField)
            ->resolve(function ($root, $args) use ($craftField) {
                $values = [];

                foreach ($root->{$craftField->handle} as $option) {
                    $values[] = StringHelper::graphQLEnumValueForString($option->value);
                }

                return $values;
            });

        // @TODO this search isn't working correctly
        $event->query->addStringArgument($craftField)
            ->type($graphqlField->getType());

        $event->mutation->addArgument($craftField)
            ->lists()
            ->type($graphqlField->getType());
    }
}
