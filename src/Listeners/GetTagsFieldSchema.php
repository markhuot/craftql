<?php

namespace markhuot\CraftQL\Listeners;

use Craft;
use craft\helpers\ElementHelper;
use markhuot\CraftQL\Events\GetFieldSchema;

class GetTagsFieldSchema
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

        if (!$schema->getRequest()->token()->can('query:tags')) {
            return;
        }

        $source = $field->settings['source'];
        if (preg_match('/taggroup:(.+)/', $source, $matches)) {
            $groupId = $matches[1];

            $schema->addField($field)
                ->lists()
                ->type($schema->getRequest()->tagGroups()->get($groupId))
                ->resolve(function ($root, $args) use ($field) {
                    return $root->{$field->handle}->all();
                });

            $event->query->addIntArgument($field);

            $inputObject = $event->mutation->createInputObjectType(ucfirst($field->handle).'TagInput');
            $inputObject->addIntArgument('id');
            $inputObject->addStringArgument('title');
            $inputObject->addStringArgument('slug');

            $event->mutation->addArgument($field)
                ->type($inputObject)
                ->lists()
                ->onSave(function ($values) use ($groupId) {
                    $group = Craft::$app->getTags()->getTagGroupById($groupId);

                    foreach ($values as &$value) {
                        if (is_numeric($value)) {
                            continue;
                        }

                        if (empty($value['slug'])) {
                            $value['slug'] = ElementHelper::createSlug($value['title']);
                        }

                        $tag = new \craft\elements\Tag();
                        $tag->groupId = $group->id;
                        $tag->fieldLayoutId = $group->fieldLayoutId;
                        $tag->title = @$value['title'];
                        $tag->slug = @$value['slug'];
                        Craft::$app->getElements()->saveElement($tag);

                        $value = $tag->id;
                    }

                    return $values;
                });
        }
    }
}
