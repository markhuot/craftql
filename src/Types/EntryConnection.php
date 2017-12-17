<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\GraphQLFields\Query\Connection\Edges as EdgesField;
use markhuot\CraftQL\Types\Entry;
use markhuot\CraftQL\Builders\Schema;

class EntryConnection extends ObjectType {

    static function edgesType($request) {
        return EntryEdge::singleton($request);
    }

    protected function fields(Request $request) {
        $schema = new Schema($request);

        $schema->addRawIntField('totalCount')
            ->nonNull();

        $schema->addRawField('pageInfo')
            ->type(PageInfo::type($request));

        $schema->addRawField('edges')
            ->lists()
            ->type(static::edgesType($request))
            ->resolve(function ($root, $args, $context, $info) {
                return array_map(function ($category) {
                    return [
                        'cursor' => '',
                        'node' => $category
                    ];
                }, $root['edges']);
            });

        $schema->addRawField('entries')
            ->lists()
            ->type(Entry::interface($request))
            ->resolve(function ($root, $args) {
                return $root['edges'];
            });

        return $schema->getFieldConfig();
    }

}