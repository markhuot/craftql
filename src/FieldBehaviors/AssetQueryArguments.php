<?php

namespace markhuot\CraftQL\FieldBehaviors;


use markhuot\CraftQL\Behaviors\FieldBehavior;

class AssetQueryArguments extends FieldBehavior {

    function initAssetQueryArguments() {
        $this->owner->addStringArgument('filename');
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addIntArgument('folderId');
        $this->owner->addIntArgument('height');
        $this->owner->addIntArgument('id');
        $this->owner->addStringArgument('kind');
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('locale');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
//        $this->owner->use(RelatedTo)
        $this->owner->addStringArgument('search');
        $this->owner->addIntArgument('size');
        $this->owner->addStringArgument('title');
        $this->owner->addIntArgument('sourceId');
        $this->owner->addIntArgument('width');
    }

}