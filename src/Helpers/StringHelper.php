<?php

namespace markhuot\CraftQL\Helpers;

use craft\models\EntryType;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Request;

class StringHelper {

    static $entryTypeMap = [];

    /**
     * Convert an Entry Type to a valid GraphQL name
     *
     * @param array $entryType
     * @return mixed|string
     */
    static function graphQLNameForEntryType(array $entryType) {
        $key = "{$entryType['id']}:{$entryType['sectionId']}";
        if (isset(static::$entryTypeMap[$key])) {
            return static::$entryTypeMap[$key];
        }

        $section = CraftQL::$plugin->sections->getById($entryType['sectionId']);

        $typeHandle = ucfirst($entryType['handle']);
        $sectionHandle = ucfirst($section['handle']);

        return static::$entryTypeMap[$key] = (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
    }

    /**
     * Convert a Craft Entry Type in to a valid GraphQL Name
     *
     * @return string
     */
    static function graphQLNameForEntryTypeSection($entryTypeId, $sectionId): string {
        $key = "{$entryTypeId}:{$sectionId}";
        if (isset(static::$entryTypeMap[$key])) {
            return static::$entryTypeMap[$key];
        }

        $entryType = CraftQL::$plugin->entryTypes->getById($entryTypeId);
        $section = CraftQL::$plugin->sections->getById($sectionId);

        $typeHandle = ucfirst($entryType['handle']);
        $sectionHandle = ucfirst($section['handle']);

        return static::$entryTypeMap[$key] = (($typeHandle == $sectionHandle) ? $typeHandle : $sectionHandle.$typeHandle);
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