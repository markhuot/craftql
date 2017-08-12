<?php

namespace markhuot\CraftQL\Fields;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\EnumType;
use yii\base\Behavior;

class EntriesBehavior extends Behavior
{
    public function getGraphQLMutationArgs() {
        $field = $this->owner;
        
        return [
            $field->handle => ['type' => Type::listOf(Type::int())]
        ];
    }

    public function getGraphQLQueryFields($request) {
        $field = $this->owner;

        // $sources = $field->sources;
        // $values = [];
        // foreach ($sources as $source) {
        //     if (!preg_match('/section:(\d+)/', $source, $matches)) {
        //         continue;
        //     }
        //     $id = $matches[1];
        //     $name = 'foo';
        //     $values[$name] = $id;
        // }
        // $enum = new EnumType([
        //     'name' => ucfirst($field->handle).'Enum',
        //     'values' => $values,
        // ]);

        // return [];

        return [
            $field->handle => [
                'type' => Type::listOf(\markhuot\CraftQL\Types\Entry::interface()),
                'description' => $field->instructions,
                'args' => \markhuot\CraftQL\Types\Entry::args($request),
                'resolve' => \markhuot\CraftQL\Types\Query::entriesFieldResolver(function($root, $args) use ($field) {
                    return $root->{$field->handle};
                }),
            ]
        ];
    }

    public function upsert($values) {
        return $values;
    }

}