<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Section extends Schema {

    function boot() {
        $this->addRawIntField('id')->nonNull();
        $this->addRawIntField('structureId');
        $this->addRawStringField('name')->nonNull();
        $this->addRawStringField('handle')->nonNull();
        $this->addRawStringField('type')->nonNull();
        $this->addRawStringField('template');
        $this->addRawIntField('maxLevels');
        $this->addRawBooleanField('hasUrls');
        $this->addRawBooleanField('enableVersioning');
        $this->addRawField('entryTypes')->lists()->type(EntryType::class);
    }

}