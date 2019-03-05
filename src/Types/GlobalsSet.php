<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class GlobalsSet extends Schema {

    function boot() {
        foreach ($this->token->globals('query') as $globalSet) {
            $this->addField($globalSet['handle'])
                ->type($this->getRequest()->getType(ucfirst($globalSet['handle'])));
        }
    }

}