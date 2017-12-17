<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Types\Category;
use markhuot\CraftQL\Builders\Schema;

class TagConnection extends ObjectType {

    protected function fields(Request $request) {
        $schema = new Schema($request);

        $schema->addRawIntField('totalCount')
            ->nonNull();

        $schema->addRawField('pageInfo')
            ->type(PageInfo::type($request));

        $schema->addRawField('edges')
            ->lists()
            ->type(TagEdge::singleton($request))
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $schema->addRawField('categories')
            ->lists()
            ->type(Tag::interface($request))
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });

        return $schema->getFieldConfig();
    }

    // static $type;

    // static function make(Request $request) {
    //     if (!empty(static::$type)) {
    //         return static::$type;
    //     }

    //     $reflect = new \ReflectionClass(static::class);

    //     return static::$type = new static([
    //         'name' => $reflect->getShortName(),
    //         'fields' => [
    //             'totalCount' => Type::nonNull(Type::int()),
    //             'pageInfo' => PageInfo::type($request),
    //             'edges' => [
    //                 'type' => Type::listOf(TagEdge::singleton($request)),
    //                 'resolve' => function ($root, $args) {
    //                     return array_map(function ($category) {
    //                         return [
    //                             'cursor' => '',
    //                             'node' => $category
    //                         ];
    //                     }, $root['edges']);
    //                 }
    //             ],
    //             'tags' => [
    //                 'type' => Type::listOf(Tag::interface($request)),
    //                 'resolve' => function ($root, $args) {
    //                     return $root['edges'];
    //                 }
    //             ],
    //         ],
    //     ]);
    // }

}