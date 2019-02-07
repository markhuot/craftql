<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class ImagerTransformedImages extends Schema {

    function boot() {
        $this->addField('assets')
            ->type(ImagerTransformedImageModel::class)
            ->lists()
            ->resolve(function ($root, $args, $context, $info) {
                return $root;
            });
    }

}