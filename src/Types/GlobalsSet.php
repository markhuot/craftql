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

class GlobalsSet extends ObjectType {

    protected function fields(Request $request) {
        return function () use ($request) {

            $schema = new Schema($request);

            foreach ($request->globals()->all() as $globalSet) {
                $schema->addRawField($globalSet->config['craftType']->handle)
                    ->type($globalSet);
            }

            // var_dump($schema->config());
            // die;

            return $schema->config();
        };
    }

}