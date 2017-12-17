<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use Craft;
use craft\elements\Entry;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class Globals extends ObjectType {

    protected function name(Request $request):string {
        return ucfirst($this->craftType->handle);
    }

    protected function fields(Request $request) {
        return function () use ($request) {
            $schema = new Schema($request);
            $schema->addFieldsByLayoutId($this->craftType->fieldLayoutId);
            return $schema->config();
        };
    }

}