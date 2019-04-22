<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Types\RelatedToInputType;
use markhuot\CraftQL\Types\SitesEnum;
use markhuot\CraftQL\Types\TagGroupsEnum;

class TagQueryArguments extends FieldBehavior {

    function initTagQueryArguments() {
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addArgument('group')->type(TagGroupsEnum::class);
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id');
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        $this->owner->addArgument('site')->type(SitesEnum::class);
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('orderBy');
        $this->owner->addStringArgument('relatedTo')->lists()->type(RelatedToInputType::class);
        $this->owner->addStringArgument('search');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('title');
    }

}
