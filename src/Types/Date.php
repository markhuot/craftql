<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Directive;

class Date extends Directive {

    protected $locations = [
        Date::FIELD
    ];

    function boot() {
        $this->addStringArgument('as');
        $this->addStringArgument('timezone');
        $this->addArgument('format')->type(DateFormatTypes::class);
        $this->addStringArgument('locale');
    }

}