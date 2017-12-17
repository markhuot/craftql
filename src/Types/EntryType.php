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
                return $this->fields($craftEntryType, $request);
            },
            'interfaces' => $this->interfaces($request),
            'craftType' => $craftEntryType,
            'id' => $craftEntryType->id,
        ];

        parent::__construct($config);
    }

    static function make($request) {
        if (!empty(static::$type)) {
            return static::$type;
        }

        return static::$type = new ObjectType([
            'name' => 'EntryType',
            'fields' => [
                'id' => ['type' => Type::nonNull(Type::int())],
                'name' => ['type' => Type::nonNull(Type::string())],
                'handle' => ['type' => Type::nonNull(Type::string())],
                'fields' => ['type' => Type::listOf(Field::make($request)), 'resolve' => function ($root, $args) {
                    return Craft::$app->fields->getLayoutById($root->fieldLayoutId)->getFields();
                }],
            ],
        ]);
    }

    function fields($craftEntryType, $request) {
        $fieldService = \Yii::$container->get('fieldService');
        $baseFields = \markhuot\CraftQL\Types\Entry::baseFields($request);
        $entryTypeFields = $fieldService->getFields($craftEntryType->fieldLayoutId, $request)['schema']->getFieldConfig();
        // var_dump($entryTypeFields->getFieldConfig());
        // die;
        return array_merge($baseFields, $entryTypeFields);
    }

    function interfaces($request) {
        return [
            \markhuot\CraftQL\Types\Entry::interface($request),
            \markhuot\CraftQL\Types\Element::interface(),
        ];
    }

    static function getName($entryType) {
        $typeHandle = ucfirst($entryType->handle);
        $sectionHandle = ucfirst($entryType->section->handle);

        return (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    function getGraphQLMutationArgs($request) {
        $fieldService = \Yii::$container->get('fieldService');

        return array_merge(\markhuot\CraftQL\Types\Entry::baseInputArgs(), $fieldService->getGraphQLMutationArgs($this->config['craftType']->fieldLayoutId, $request));
    }

    function handle() {
        return $this->config['craftType']->handle;
    }

    function upsert($request) {
        return function ($root, $args) use ($request) {
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
                $entry->sectionId = $this->config['craftType']->section->id;
                $entry->typeId = $this->config['craftType']->id;
            }

            if (isset($args['authorId'])) {
                $entry->authorId = $args['authorId'];
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

            $fieldService = \Yii::$container->get('fieldService');

            foreach ($fields as $handle => &$value) {
                $field = Craft::$app->fields->getFieldByHandle($handle);
                $value = $fieldService->mutateValueForField($request, $field, $value, $entry);
                // $value = $field->upsert($value, $entry);
            }

            $entry->setFieldValues($fields);

            Craft::$app->elements->saveElement($entry);

            return $entry;
        };
    }

}