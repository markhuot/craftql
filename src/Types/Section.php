<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Section extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addIntField('structureId');
        $this->addStringField('name')->nonNull();
        $this->addStringField('handle')->nonNull();
        $this->addStringField('type')->nonNull();
        $this->addStringField('template');
        $this->addIntField('maxLevels');
        $this->addBooleanField('hasUrls');
        $this->addBooleanField('enableVersioning');
        $this->addField('entryTypes')->lists()->type(EntryType::class);
    }

}