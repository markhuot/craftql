<?php

namespace markhuot\CraftQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\FieldBehaviors\EntryMutationArguments;

class Mutation extends Schema {

    function boot() {

        foreach ($this->request->entryTypes()->all('mutate') as $entryType) {
            $this->addField('upsert'.$entryType->getName())
                ->type($entryType)
                ->use(new EntryMutationArguments);
        }

        if ($this->request->globals()->count()) {
            /** @var \markhuot\CraftQL\Types\Globals $globalSet */
            foreach ($this->request->globals()->all() as $globalSet) {
                $upsertField = $this->addField('upsert'.$globalSet->getName().'Globals')
                    ->type($globalSet)
                    ->addArgumentsByLayoutId($globalSet->getContext()->fieldLayoutId);

                $upsertField->resolve(function ($root, $args) use ($globalSet, $upsertField) {
                        $globalSetElement = $globalSet->getContext();

                        foreach ($args as $handle => &$value) {
                            $callback = $upsertField->getArgument($handle)->getOnSave();
                            if ($callback) {
                                $value = $callback($value);
                            }
                        }

                        $globalSetElement->setFieldValues($args);
                        Craft::$app->getElements()->saveElement($globalSetElement);
                        return $globalSetElement;
                    });
            }
        }

        // $fields['upsertField'] = [
        //     'type' => \markhuot\CraftQL\Types\Entry::interface($request),
        //     'args' => [
        //         'id' => Type::nonNull(Type::int()),
        //         'json' => Type::nonNull(Type::string()),
        //     ],
        //     'resolve' => function ($root, $args) {
        //         $entry = \craft\elements\Entry::find();
        //         $entry->id($args['id']);
        //         $entry = $entry->first();

        //         $json = json_decode($args['json'], true);
        //         $fieldData = [];
        //         foreach ($json as $fieldName => $value) {
        //             if (in_array($fieldName, ['title'])) {
        //                 $entry->{$fieldName} = $value;
        //             }
        //             else {
        //                 $fieldData[$fieldName] = $value;
        //             }
        //         }

        //         if (!empty($fieldData)) {
        //             $entry->setFieldValues($fieldData);
        //         }

        //         Craft::$app->elements->saveElement($entry);

        //         return $entry;
        //     },
        // ];
    }

}