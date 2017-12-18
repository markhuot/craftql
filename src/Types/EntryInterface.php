<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;

class EntryInterface extends Schema {

    // static $interfaces = [];
    // static $baseFields;

    function boot() {
        // if (!empty(static::$baseFields)) {
        //     return static::$baseFields;
        // }

        // $schema = new Schema($request);
        $this->addRawStringField('elementType')->nonNull()->resolve('Entry');
        $this->addRawIntField('id')->nonNull();

        if ($this->request->token()->can('query:entry.author')) {
            $this->addRawField('author')->type(\markhuot\CraftQL\Types\User::type($this->request))->nonNull();
        }

        $this->addRawStringField('title')->nonNull();
        $this->addRawStringField('slug')->nonNull();
        $this->addRawDateField('dateCreated');
        $this->addRawDateField('dateUpdated');
        $this->addRawDateField('expiryDate');
        $this->addRawBooleanField('enabled')->nonNull();
        $this->addRawStringField('status')->nonNull();
        $this->addRawStringField('uri');
        $this->addRawStringField('url');
        $this->addRawField('section')->type(\markhuot\CraftQL\Types\Section::type());
        $this->addRawField('type')->type(\markhuot\CraftQL\Types\EntryType::singleton($this->request));
        // $this->addRawField('ancestors')->type(Type::listOf(\markhuot\CraftQL\Types\EntryInterface::singleton($this->request)));
        // $this->addRawField('children')->type(Type::listOf(\markhuot\CraftQL\Types\EntryInterface::singleton($this->request)));
        // $this->addRawField('descendants')->type(Type::listOf(\markhuot\CraftQL\Types\EntryInterface::singleton($this->request)));
        $this->addRawField('hasDescendants')->type(Type::nonNull(Type::boolean()));
        $this->addRawField('level')->type(Type::int());
        // $this->addRawField('parent')->type(\markhuot\CraftQL\Types\EntryInterface::singleton($this->request));
        // $this->addRawField('siblings')->type(Type::listOf(\markhuot\CraftQL\Types\EntryInterface::singleton($this->request)));

        // $fields['json'] = ['type' => Type::string(), 'resolve' => function($root, $args) {
        //     return json_encode($root->toArray());
        // }];

        // return static::$baseFields = $schema->getFields();
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    static function resolveType($entry) {
        return \markhuot\CraftQL\Types\EntryTypeDerivitive::entryTypeObjectName($entry->type);
    }

    // static function singleton($request) {
    //     $reflect = new \ReflectionClass(static::class);
    //     $shortName = $reflect->getShortName();

    //     if (!empty(static::$interfaces[$shortName])) {
    //         return static::$interfaces[$shortName];
    //     }

    //     return static::$interfaces[$shortName] = new InterfaceType([
    //         'name' => $shortName,
    //         'description' => 'An entry in Craft',

    //         // this has to be a callback because the `user` field references a User type
    //         // that could have an Entries custom field. This is a problem because we have
    //         // a circullar reference. Our EntryInterface defines a User which defines an
    //         // Entries field which relies on the EntryInterface. The callback here ensures
    //         // that the nested Entries field gets a resolved interface.
    //         'fields' => function () use ($request) {
    //             $fields = [];
    //             foreach (static::baseFields($request) as $field) {
    //                 $fields[$field->getName()] = $field->getConfig();
    //             }
    //             return $fields;
    //         },

    //         'resolveType' => function ($entry) {
    //             return static::resolveType($entry);
    //         }
    //     ]);
    // }

}