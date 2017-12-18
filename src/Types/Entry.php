<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class Entry {

    static $interfaces = [];
    static $baseFields;

    /**
     * An input object (for argument lists) that controls how any
     * related elements are searched.
     *
     * @var InputObjectType
     */
    static $relatedToInputObject;

    static function baseInputArgs() {
        return [
            'id' => ['type' => Type::int()],
            'authorId' => ['type' => Type::int()],
            'title' => ['type' => Type::string()],
        ];
    }

    /**
     * An input object to query entries by relationship
     *
     * @return InputObjectType
     */
    static function relatedToInputObject() {
        if (static::$relatedToInputObject) {
            return static::$relatedToInputObject;
        }

        return static::$relatedToInputObject = new InputObjectType([
            'name' => 'RelatedTo',
            'fields' => [
                'element' => Type::id(),
                'sourceElement' => Type::id(),
                'targetElement' => Type::id(),
                'field' => Type::string(),
                'sourceLocale' => Type::string(),
            ],
        ]);
    }

    static function args($request) {
        return [
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::listOf(Type::int()),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::listOf(Type::int()),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::id(),
            'positionedBefore' => Type::id(),
            'postDate' => Type::string(),
            'prevSiblingOf' => Type::id(),
            'relatedTo' => Type::listOf(static::relatedToInputObject()),
            'orRelatedTo' => Type::listOf(static::relatedToInputObject()),
            'search' => Type::string(),
            'section' => Type::listOf($request->sections()->enum()),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            // 'type' => Type::listOf($request->entryTypes()->enum()),
            'uri' => Type::string(),
        ];
    }

}