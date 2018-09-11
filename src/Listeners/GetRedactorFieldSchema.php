<?php

namespace markhuot\CraftQL\Listeners;

class GetRedactorFieldSchema
{
    static $outputSchema;

    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $outputSchema = static::type($event->schema);

        $event->schema->addField($event->sender)->type($outputSchema);
        $event->query->addStringArgument($event->sender);
        $event->mutation->addStringArgument($event->sender);
    }

    static function type($schema) {
        if (static::$outputSchema) {
            return static::$outputSchema;
        }

        $outputSchema = $schema->createObjectType('RedactorFieldData');

        $outputSchema->addIntField('totalPages')
            ->resolve(function ($root, $args) {
                return $root->getTotalPages();
            });

        $outputSchema->addStringField('content')
            ->arguments(function ($field) {
                $field->addIntArgument('page');
            })
            ->resolve(function ($root, $args) {
                if (!empty($args['page'])) {
                    return (string)$root->getPage($args['page']);
                }

                return (string)$root;
            });

        return static::$outputSchema=$outputSchema;
    }
}
