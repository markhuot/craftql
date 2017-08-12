<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class EntryType extends ObjectType {

    public $craftType;
    static $rawCraftEntryTypes = [];
    static $types = [];
    static $type;
    static $typeArgEnum;
    static $sectionArgEnum;

    public function __construct($craftEntryType, $request) {
        $config = [
            'name' => static::getName($craftEntryType),
            'fields' => function () use ($craftEntryType, $request) {
                $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
                $baseFields = \markhuot\CraftQL\Types\Entry::baseFields();
                $entryTypeFields = $fieldService->getFields($craftEntryType->fieldLayoutId, $request);
                return array_merge($baseFields, $entryTypeFields);
            },
            'interfaces' => [
                \markhuot\CraftQL\Types\Entry::interface(),
                \markhuot\CraftQL\Types\Element::interface(),
            ],
            'craftType' => $craftEntryType,
            'id' => $craftEntryType->id,
        ];

        parent::__construct($config);
    }

    static function getName($entryType) {
        $typeHandle = ucfirst($entryType->handle);
        $sectionHandle = ucfirst($entryType->section->handle);

        return (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    static function type() {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'EntryType',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'handle' => ['type' => Type::nonNull(Type::string())],
            ],
        ]);
    }

    function args() {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);

        return array_merge(\markhuot\CraftQL\Types\Entry::baseInputArgs(), $fieldService->getArgs($this->craftType->fieldLayoutId));
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

            foreach ($fields as $handle => &$value) {
                $field = Craft::$app->fields->getFieldByHandle($handle);
                $value = $field->upsert($value);
            }

            $entry->setFieldValues($fields);

            Craft::$app->elements->saveElement($entry);
            
            return $entry;
        };
    }

}