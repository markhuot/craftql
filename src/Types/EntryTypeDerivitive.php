<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;
use markhuot\CraftQL\Builders\Schema;

class EntryTypeDerivitive extends Schema {

    public $craftType;
    static $rawCraftEntryTypes = [];
    static $types = [];
    static $type;
    static $typeArgEnum;
    static $sectionArgEnum;

    function getName(): string {
        return static::entryTypeObjectName($this->context);
    }

    function getInterfaces(): array {
        return [
            \markhuot\CraftQL\Types\EntryInterface::class,
            \markhuot\CraftQL\Types\Element::interface(),
        ];
    }

    static function entryTypeObjectName($entryType) {
        $typeHandle = ucfirst($entryType->handle);
        $sectionHandle = ucfirst($entryType->section->handle);

        return (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    function getFields(): array {
        $fieldService = \Yii::$container->get('fieldService');
        // $baseFields = \markhuot\CraftQL\Types\EntryInterface::baseFields($this->request);
        $baseFields = [];
        $entryTypeFields = $fieldService->getFields($this->context->fieldLayoutId, $this->request)['schema']->getFields();
        return array_merge($baseFields, $entryTypeFields);
    }

    // function getGraphQLMutationArgs($request) {
    //     $fieldService = \Yii::$container->get('fieldService');

    //     return array_merge(\markhuot\CraftQL\Types\Entry::baseInputArgs(), $fieldService->getGraphQLMutationArgs($this->config['craftType']->fieldLayoutId, $request));
    // }

    // function handle() {
    //     return $this->config['craftType']->handle;
    // }

    // function upsert($request) {
    //     return function ($root, $args) use ($request) {
    //         if (!empty($args['id'])) {
    //             $criteria = Entry::find();
    //             $criteria->id($args['id']);
    //             $entry = $criteria->one();
    //             if (!$entry) {
    //                 throw new \Exception('Could not find an entry with id '.$args['id']);
    //             }
    //         }
    //         else {
    //             $entry = new Entry();
    //             $entry->sectionId = $this->config['craftType']->section->id;
    //             $entry->typeId = $this->config['craftType']->id;
    //         }

    //         if (isset($args['authorId'])) {
    //             $entry->authorId = $args['authorId'];
    //         }

    //         if (isset($args['title'])) {
    //             $entry->title = $args['title'];
    //         }

    //         $fields = $args;
    //         unset($fields['id']);
    //         unset($fields['title']);
    //         unset($fields['sectionId']);
    //         unset($fields['typeId']);
    //         unset($fields['authorId']);

    //         $fieldService = \Yii::$container->get('fieldService');

    //         foreach ($fields as $handle => &$value) {
    //             $field = Craft::$app->fields->getFieldByHandle($handle);
    //             $value = $fieldService->mutateValueForField($request, $field, $value, $entry);
    //             // $value = $field->upsert($value, $entry);
    //         }

    //         $entry->setFieldValues($fields);

    //         Craft::$app->elements->saveElement($entry);

    //         return $entry;
    //     };
    // }

}