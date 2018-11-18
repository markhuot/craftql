<?php

namespace markhuot\CraftQL\Types;

use Craft;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\Request;

class GlobalSets {

    static function craftQlFields(Schema $type, Request $request) {
        foreach (Craft::$app->globals->allSets as $set) {
            $type->addField($set->handle)
                ->type($request->registry()->get(ucfirst($set->handle)));
        }
    }

}