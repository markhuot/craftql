<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;

class ElementInterface extends InterfaceBuilder {

    function boot() {
        $this->addStringField('elementType');
    }

    function getResolveType() {
        return function ($element) {
            switch ($element->elementType) {
                // @TODO add Tag elements
                case 'Category':
                    return ucfirst($element->group->handle);
                    break;
                case 'Entry':
                    return ucfirst($element->section->handle);
                    break;
            }
        };
    }

}