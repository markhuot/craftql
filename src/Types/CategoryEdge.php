<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class CategoryEdge extends Edge {

    static function baseFields($request) {
        $fields = [];

        $fields['cursor'] = Type::string();
        $fields['node'] = [
            'type' => \markhuot\CraftQL\Types\Category::interface($request),
            'resolve' => function ($root, $args, $context, $info) {
                return $root['node'];
            }
        ];
        $fields['relatedTo'] = (new \markhuot\CraftQL\GraphQLFields\Edge\RelatedTo($request))->toArray();

        return $fields;
    }

}