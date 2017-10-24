<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

abstract class Edge extends ObjectType {

    static function baseFields($request) {
        $fields = [];
        $fields['cursor'] = Type::string();
        $fields['node'] = ['type' => \markhuot\CraftQL\Types\Entry::interface($request), 'resolve' => function ($root, $args, $context, $info) {
            return $root['node'];
        }];
        return $fields;
    }

    static function make($request) {
        $reflect = new \ReflectionClass(static::class);
        $shortName = $reflect->getShortName();

        return new static([
            'name' => $shortName,

            // this has to be a callback because `EntryEdge` returns an `EntryConnection`
            // which references our own `EntryEdge` that creates an immediate circular
            // reference. The `fields` callback allows `graphql-php` to work around the
            // circular reference.
            'fields' => function () use ($request) {
                return static::baseFields($request);
            },
        ]);
    }

}