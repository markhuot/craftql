<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class SectionSiteSettings extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addIntField('siteId')->nonNull();
        $this->addBooleanField('enabledByDefault')->nonNull();
        $this->addBooleanField('hasUrls')->nonNull();
        $this->addStringField('uriFormat')->nonNull();
        $this->addStringField('template')->nonNull();
    }

}