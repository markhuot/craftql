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
            'type' => Type::listOf($request->entryTypes()->enum()),
            'uri' => Type::string(),
        ];
    }

    static function baseFields($request) {
        if (!empty(static::$baseFields)) {
            return static::$baseFields;
        }

        $schema = new Schema($request);
        $schema->addRawStringField('elementType')->nonNull()->resolve('Entry');
        $schema->addRawIntField('id')->nonNull();

        if ($request->token()->can('query:entry.author')) {
            $schema->addRawField('author')->type(\markhuot\CraftQL\Types\User::type($request))->nonNull();
        }

        $schema->addRawStringField('title')->nonNull();
        $schema->addRawStringField('slug')->nonNull();
        $schema->addRawDateField('dateCreated');
        $schema->addRawDateField('dateUpdated');
        $schema->addRawDateField('expiryDate');
        $schema->addRawBooleanField('enabled')->nonNull();
        $schema->addRawStringField('status')->nonNull();
        $schema->addRawStringField('uri');
        $schema->addRawStringField('url');
        $schema->addRawField('section')->type(\markhuot\CraftQL\Types\Section::type());
        $schema->addRawField('type')->type(\markhuot\CraftQL\Types\EntryType::make($request));
        $schema->addRawField('ancestors')->type(Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)));
        $schema->addRawField('children')->type(Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)));
        $schema->addRawField('descendants')->type(Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)));
        $schema->addRawField('hasDescendants')->type(Type::nonNull(Type::boolean()));
        $schema->addRawField('level')->type(Type::int());
        $schema->addRawField('parent')->type(\markhuot\CraftQL\Types\Entry::interface($request));
        $schema->addRawField('siblings')->type(Type::listOf(\markhuot\CraftQL\Types\Entry::interface($request)));

        // $fields['json'] = ['type' => Type::string(), 'resolve' => function($root, $args) {
        //     return json_encode($root->toArray());
        // }];

        return static::$baseFields = $schema->getFieldConfig();
    }

    static function resolveType($entry) {
        return \markhuot\CraftQL\Types\EntryType::getName($entry->type);
    }

    static function interface($request) {
        $reflect = new \ReflectionClass(static::class);
        $shortName = $reflect->getShortName();

        if (!empty(static::$interfaces[$shortName])) {
            return static::$interfaces[$shortName];
        }

        return static::$interfaces[$shortName] = new InterfaceType([
            'name' => $shortName.'Interface',
            'description' => 'An entry in Craft',

            // this has to be a callback because the `user` field references a User type
            // that could have an Entries custom field. This is a problem because we have
            // a circullar reference. Our EntryInterface defines a User which defines an
            // Entries field which relies on the EntryInterface. The callback here ensures
            // that the nested Entries field gets a resolved interface.
            'fields' => function () use ($request) {
                return static::baseFields($request);
            },

            'resolveType' => function ($entry) {
                return static::resolveType($entry);
            }
        ]);
    }

}