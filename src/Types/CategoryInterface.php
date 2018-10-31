<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;

class CategoryInterface extends InterfaceBuilder {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addStringField('title')->nonNull();
        $this->addStringField('slug');
        $this->addStringField('uri');
        $this->addIntField('level');
        $this->addStringField('group')->type(CategoryGroup::class);
        $this->addField('children')->type(CategoryInterface::class)->lists()->use(new CategoryQueryArguments);
        $this->addField('childrenConnection')->type(CategoryConnection::class)->use(new CategoryQueryArguments);
        $this->addField('parent')->type(CategoryInterface::class);
        $this->addField('next')->type(CategoryInterface::class);
        $this->addField('nextSibling')->type(CategoryInterface::class);
        $this->addField('prev')->type(CategoryInterface::class);
        $this->addField('prevSibling')->type(CategoryInterface::class);
    }

    function getResolveType() {
        return function ($category) {
            return ucfirst($category->group->handle).'Category';
        };
    }

}