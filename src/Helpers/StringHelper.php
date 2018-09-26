<?php

namespace markhuot\CraftQL\Helpers;

use craft\base\Element;
use craft\models\EntryType;
use markhuot\CraftQL\Request;

class StringHelper {

    /**
     * Convert a Craft Entry in to a valid GraphQL Entry Type Name
     *
     * This caches better than the `graphQLNameForEntryType` which makes a DB query for the _full_
     * entry type, which we don't necessarily need
     *
     * @return string
     */
    static function graphQLNameForEntry(Request $request, Element $entry): string {
        $sectionHandle = ucfirst($request->sections()->get($entry->sectionId)->getContext()->handle);
        $typeHandle = ucfirst($request->entryTypes()->get($entry->typeId)->getContext()->handle);

        return (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    /**
     * Convert a Craft Entry Type in to a valid GraphQL Name
     *
     * @param CraftEntryType $entryType
     * @return string
     */
    static function graphQLNameForEntryType(EntryType $entryType): string {
        $typeHandle = ucfirst($entryType->handle);
        $sectionHandle = ucfirst($entryType->section->handle);

        return (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    /**
     * Convert a Craft Entry Type in to a valid GraphQL Enum Value
     *
     * @param CraftEntryType $entryType
     * @return string
     */
    static function graphQLEnumValueForString($string): string {
        $string = preg_replace('/[^a-z0-9_]+/i', ' ', $string);
        $string = preg_replace_callback('/\s+(.)/', function ($match) {
            return ucfirst($match[1]);
        }, $string);
        return $string;
    }

}