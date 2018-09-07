<?php

namespace markhuot\CraftQL\Helpers;

use craft\models\EntryType;

class StringHelper {

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