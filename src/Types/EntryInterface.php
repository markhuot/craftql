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

    function boot() {
        $this->addRawIntField('id')->nonNull();

        if ($this->request->token()->can('query:entry.author')) {
            $this->addRawField('author')->type(User::class)->nonNull();
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
        $this->addRawField('section')->type(Section::class);
        $this->addRawField('type')->type(EntryType::class);
        $this->addRawField('ancestors')->lists()->type(EntryInterface::class);
        $this->addRawField('children')->lists()->type(EntryInterface::class);
        $this->addRawField('descendants')->lists()->type(EntryInterface::class);
        $this->addRawField('hasDescendants')->type(Type::nonNull(Type::boolean()));
        $this->addRawField('level')->type(Type::int());
        $this->addRawField('parent')->type(EntryInterface::class);
        $this->addRawField('siblings')->lists()->type(EntryInterface::class);
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getGraphQLConfig());
    }

    function getResolveType() {
        return function ($entry) {
            return \markhuot\CraftQL\Types\Entry::entryTypeObjectName($entry->type);
        };
    }

}