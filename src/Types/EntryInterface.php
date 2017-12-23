<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;
use markhuot\CraftQL\Helpers\StringHelper;

class EntryInterface extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();

        if ($this->request->token()->can('query:entry.author')) {
            $this->addField('author')->type(User::class)->nonNull();
        }

        $this->addStringField('title')->nonNull();
        $this->addStringField('slug')->nonNull();
        $this->addDateField('dateCreated');
        $this->addDateField('dateUpdated');
        $this->addDateField('expiryDate');
        $this->addBooleanField('enabled')->nonNull();
        $this->addStringField('status')->nonNull();
        $this->addStringField('uri');
        $this->addStringField('url');
        $this->addField('section')->type(Section::class);
        $this->addField('type')->type(EntryType::class);
        $this->addField('ancestors')->lists()->type(EntryInterface::class);
        $this->addField('children')->lists()->type(EntryInterface::class);
        $this->addField('descendants')->lists()->type(EntryInterface::class);
        $this->addField('hasDescendants')->type(Type::nonNull(Type::boolean()));
        $this->addField('level')->type(Type::int());
        $this->addField('parent')->type(EntryInterface::class);
        $this->addField('siblings')->lists()->type(EntryInterface::class);
    }

    function getGraphQLObject() {
        return new InterfaceType($this->getConfig());
    }

    function getResolveType() {
        return function ($entry) {
            return StringHelper::graphQLNameForEntryType($entry->type);
        };
    }

}