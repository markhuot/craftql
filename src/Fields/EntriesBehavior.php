<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\UnionType;
use yii\base\Behavior;

class EntriesBehavior extends Behavior
{
    // static $unions = [];

    public function getGraphQLMutationArgs() {
        $field = $this->owner;
        
        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    // private function getSectionEntryTypes($request, $id) {
    //     $entryTypes = [];
    //     $section = $request->sections()->repository()->get($id);

    //     foreach ($section->entryTypes as $entryType) {
    //         if ($entryType=$request->entryTypes()->get($entryType->id)) {
    //             $entryTypes[] = $entryType;
    //         }
    //     }

    //     return $entryTypes;
    // }

    // public function getUnion($field, $request) {
    //     if (isset(static::$unions[$field->id])) {
    //         return static::$unions[$field->id];
    //     }

    //     $types = [];

    //     $sources = $field->sources;
    //     if (is_array($sources)) {
    //         foreach ($sources as $source) {
    //             if (!preg_match('/section:(\d+)/', $source, $matches)) {
    //                 continue;
    //             }
    //             $id = $matches[1];
    //             if ($entryTypes=$this->getSectionEntryTypes($request, $id)) {
    //                 $types = array_merge($types, $entryTypes);
    //             }
    //         }
    //     }
    //     else if ($sources == '*') {
    //         $types = $request->entryTypes()->all();
    //     }

    //     return static::$unions[$field->id] = new UnionType([
    //         'name' => ucfirst($field->handle).'Union',
    //         'types' => $types,
    //         'resolveType' => function ($entry) {
    //             return \markhuot\CraftQL\Types\EntryType::getName($entry->type);
    //         }
    //     ]);
    // }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        // $union = $this->getUnion($field, $request);

        return [
            $field->handle => [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)),
                // 'type' => Type::listOf($union),
                'description' => $field->instructions,
                'args' => \markhuot\CraftQL\Types\Entry::args($request),
                'resolve' => $request->entriesCriteria(function($root, $args) use ($field) {
                    return $root->{$field->handle};
                }),
            ]
        ];
    }

    public function upsert($values) {
        return $values;
    }

}