<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class EntryType extends ObjectType {

    public $craftType;
    static $types = [];

    static function make($entryType) {
        if (!empty(static::$types[$entryType->id])) {
            return static::$types[$entryType->id];
        }

        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $fields = \markhuot\CraftQL\Types\Entry::baseFields();
        $fields = array_merge($fields, $fieldService->getFields($entryType->fieldLayoutId));

        $type = static::$types[$entryType->id] = new static([
            'name' => static::getName($entryType),
            'fields' => $fields,
            'interfaces' => [
                // $sectionType,
                \markhuot\CraftQL\Types\Entry::interface(),
                \markhuot\CraftQL\Types\Element::interface(),
            ],
        ]);
        $type->craftType = $entryType;
        return $type;
    }

    static function get($entryTypeId) {
        return @static::$types[$entryTypeId];
    }

    static function all() {
        if (!empty(static::$types)) {
            return static::$types;
        }

        foreach (Craft::$app->sections->allSections as $section) {
            foreach ($section->entryTypes as $entryType) {
                static::$types[$entryType->id] = \markhuot\CraftQL\Types\EntryType::make($entryType);
            }
        }

        return static::$types;
    }

    static function getName($entryType) {
        if ($entryType->section->handle == $entryType->handle) {
            return ucfirst($entryType->handle);
        }
        
        return ucfirst($entryType->section->handle).ucfirst($entryType->handle);
    }

    function name() {
        return static::getName($this->craftType);
    }

    function args() {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        return $fieldService->getArgs($this->craftType->fieldLayoutId);
    }

    function fields() {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        $fields = \markhuot\CraftQL\Types\Entry::baseFields();
        $fields = array_merge($fields, $fieldService->getFields($entryType->fieldLayoutId));

        return $fields;
    }

    function upsert() {
        return function ($root, $args) {
            if (!empty($args['id'])) {
                $criteria = Entry::find();
                $criteria->id($args['id']);
                $entry = $criteria->one();
                if (!$entry) {
                    throw new \Exception('Could not find an entry with id '.$args['id']);
                }
            }
            else {
                $entry = new Entry();
                $entry->sectionId = $this->craftType->section->id;
                $entry->typeId = $this->craftType->id;
            }

            if (isset($args['authorId']) || !$entry->authorId) {
                $entry->authorId = @$args['authorId'] ?: Craft::$app->getUser()->getIdentity()->id;
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
            // var_dump($fields);
            // die;

            $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

            foreach ($fields as $handle => &$value) {
                $value = $fieldService->upsertFieldValue($handle, $value);
            }

            $entry->setFieldValues($fields);

            Craft::$app->elements->saveElement($entry);
            
            return $entry;
        };
    }

}