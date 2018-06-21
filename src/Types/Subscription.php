<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Subscription extends Schema {

    function boot() {
        $this->addField('beforeSaveEntry')
            ->type(EntryInterface::class)
            ->resolve(function ($root, $args, $context, $info) {
                return \craft\elements\Entry::find()->one();
            });
    }

}