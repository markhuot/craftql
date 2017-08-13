<?php

namespace markhuot\CraftQL\Models;

use craft\db\ActiveRecord;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Token extends ActiveRecord
{
    public static function forUser(): Token
    {
        $token = new static;
        $token->scopes = json_encode([
            'query:entries' => 1,
            'query:users' => 1,
        ]);
        return $token;
    }

    /**
     * @return string The associated database table name
     */
    public static function tableName(): string
    {
        return '{{%craftql_tokens}}';
    }

    function getScopeArray(): array
    {
        return json_decode($this->scopes, true);
    } 

    function can($do): bool {
        return @$this->scopeArray[$do] ?: false;
    }

    function canNot($do): bool {
        return !$this->can($do);
    }

    function mutableEntryTypeIds(): array {
        $ids = [];

        foreach ($this->scopeArray as $scope => $enabled) {
            if ($enabled && preg_match('/mutation:entryType:(\d+)/', $scope, $matches)) {
                $ids[] = $matches[1];
            }
        }

        return $ids;
    }

    // function queryableEntryTypeIds(): array {
    //     $ids = [];

    //     foreach ($this->scopeArray as $scope => $enabled) {
    //         if ($enabled && preg_match('/query:entryType:(\d+)/', $scope, $matches)) {
    //             $ids[] = $matches[1];
    //         }
    //     }

    //     return $ids;
    // }

    // private $entryTypeEnum;

    // function entryTypeEnum() {
    //     if ($this->entryTypeEnum) {
    //         return $this->entryTypeEnum;
    //     }

    //     $entryTypeEnumValues = [];
    //     // $sectionEnumValues = [];

    //     foreach (\markhuot\CraftQL\Repositories\EntryType::all() as $entryType) {
    //         if (in_array($entryType->id, $this->queryableEntryTypeIds())) {
    //             $name = \markhuot\CraftQL\Types\EntryType::getName($entryType);
    //             $entryTypeEnumValues[$name] = $entryType->id;
    //             // $sectionEnumValues[$entryType->section->handle] = $entryType->section->id;
    //         }
    //     }

    //     return $this->entryTypeEnum = new EnumType([
    //         'name' => 'EntryTypeEnum',
    //         'values' => $entryTypeEnumValues,
    //     ]);

    //     // $this->sectionArgEnum = new EnumType([
    //     //     'name' => 'SectionEnum',
    //     //     'values' => $sectionEnumValues,
    //     // ]);
    // }

    function allowsMatch($regex): bool {
        $scopes = [];

        foreach ($this->scopeArray as $key => $value) {
            if ($value > 0 && preg_match($regex, $key)) {
                $scopes[] = $key;
            }
        }

        return count($scopes) > 0;
    }
}