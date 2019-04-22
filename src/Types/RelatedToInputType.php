<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InputSchema;

class RelatedToInputType extends InputSchema {

    function boot() {
        $this->addIntArgument('element')->lists();
        $this->addBooleanArgument('elementIsEdge');
        $this->addIntArgument('sourceElement')->lists();
        $this->addBooleanArgument('sourceElementIsEdge');
        $this->addIntArgument('targetElement')->lists();
        $this->addBooleanArgument('targetElementIsEdge');
        $this->addStringArgument('field');
        $this->addStringArgument('sourceLocale');
    }

}
