<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;

class TagInterface extends InterfaceBuilder {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addStringField('title')->nonNull();
        $this->addStringField('slug')->nonNull();
        $this->addField('group')->nonNull()->type(TagGroup::class);
    }

    function getResolveType() {
        return function ($tag) {
            return ucfirst($tag->group->handle).'Tags';
        };
    }

}