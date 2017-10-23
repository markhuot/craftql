<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;

class EntryTypeDraft extends EntryType {

    function interfaces($request) {
        return [
            \markhuot\CraftQL\Types\EntryDraft::interface($request),
            \markhuot\CraftQL\Types\Element::interface(),
        ];
    }

    function fields($craftEntryType, $request) {
        $fieldService = \Yii::$container->get(\markhuot\CraftQL\Services\FieldService::class);
        $baseFields = \markhuot\CraftQL\Types\EntryDraft::baseFields($request);
        $entryTypeFields = $fieldService->getFields($craftEntryType->fieldLayoutId, $request);
        return array_merge($baseFields, $entryTypeFields);
    }

    static function getName($entryType) {
        return parent::getName($entryType).'Draft';
    }

}