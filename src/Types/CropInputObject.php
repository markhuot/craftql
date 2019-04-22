<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class CropInputObject extends InputSchema {

    function boot() {
        $this->addIntArgument('width');
        $this->addIntArgument('height');
        $this->addIntArgument('quality');
        $this->addArgument('position')->type(PositionInputEnum::class);
        $this->addArgument('format')->type(CropFormatInputEnum::class);
    }

}