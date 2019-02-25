<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;

class TagQueryArguments extends FieldBehavior {

    function initTagQueryArguments() {
        $this->owner->addBooleanArgument('fixedOrder');
        // $this->owner->addArgument('group')->type($this->owner->getRequest()->tagGroups()->enum());
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id');
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        // $this->owner->addArgument('site')->type($this->owner->request->sites()->enum());
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('orderBy');
        // $this->owner->addStringArgument('relatedTo')->lists()->type(Entry::relatedToInputObject());
        $this->owner->addStringArgument('search');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('title');
    }

}
