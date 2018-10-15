<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\Helpers\StringHelper;

class EntryInterface extends InterfaceBuilder {

    function boot() {
        $this->addIntField('id')->nonNull();

        if ($this->request->token()->can('query:entry.author')) {
            $this->addField('author')->type(User::class)->nonNull();
        }

        $this->addStringField('title')->nonNull();
        $this->addStringField('slug')->nonNull();
        $this->addDateField('dateCreated')->nonNull();
        $this->addDateField('dateUpdated')->nonNull();
        $this->addDateField('expiryDate');
        $this->addDateField('postDate');
        $this->addBooleanField('enabled')->nonNull();
        $this->addStringField('status')->nonNull();
        $this->addStringField('uri');
        $this->addStringField('url');

        if ($this->request->token()->can('query:sections')) {
            $this->addField('section')->type(Section::class);
            $this->addField('type')->type(EntryType::class);
        }

        $this->addField('ancestors')->lists()->type(EntryInterface::class);

        $this->addField('children')
            ->lists()
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->request->entries($root->{$info->fieldName}, $root, $args, $context, $info);
            });

        $this->addField('descendants')->lists()->type(EntryInterface::class);
        $this->addBooleanField('hasDescendants')->nonNull();
        $this->addIntField('level');
        $this->addField('parent')->type(EntryInterface::class);
        $this->addField('siblings')->lists()->type(EntryInterface::class);
    }

    function getResolveType() {
        return function ($entry) {
            return StringHelper::graphQLNameForEntryType($entry->type);
        };
    }

}