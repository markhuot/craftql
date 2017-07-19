<?php

namespace markhuot\CraftQL\GraphQL\Types;

use yii\base\Component;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class Mutation extends Component {

    function getType() {
        $fields = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'upsertEntry' => [
                    'type' => \markhuot\CraftQL\GraphQL\Types\Entry::interface(),
                    'args' => $fields->getAllFieldArgs(),
                    'resolve' => function ($root, $args) {
                        if (!empty($args['id'])) {
                            $criteria = Entry::find();
                            $criteria->id($args['id']);
                            $entry = $criteria->one();
                            if (!$entry) {
                                throw new \Exception('Could not find an entry with id '.$args['id']);
                            }
                        }
                        
                        if (
                            !empty($args['sectionId']) &&
                            !empty($args['typeId']) &&
                            !empty($args['authorId'])
                        ) {
                            $entry = new Entry();
                            $entry->sectionId = $args['sectionId'];
                            $entry->typeId = $args['typeId'];
                            $entry->authorId = $args['authorId'];
                        }
                        else {
                            throw new \Exception('Could not create an entry. `sectionId`, `typeId`, and `authorId` must be set.');
                        }

                        if (isset($args['title'])) {
                            $entry->title = $args['title'];
                        }

                        $fields = $args;
                        unset($fields['id']);
                        unset($fields['title']);
                        unset($fields['sectionId']);
                        unset($fields['typeId']);
                        unset($fields['authorId']);
                        $entry->setFieldValues($fields);

                        Craft::$app->elements->saveElement($entry);
                        
                        return $entry;
                    }
                ]
            ],
        ]);
    }

}