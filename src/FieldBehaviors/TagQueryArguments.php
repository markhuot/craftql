<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class TagQueryArguments extends Behavior {

    function initTagQueryArguments() {
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addArgument('group')->type($request->tagGroups()->enum());
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id');
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('locale');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        // $this->owner->addStringArgument('relatedTo')->lists()->type(Entry::relatedToInputObject());
        $this->owner->addStringArgument('search');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('title');
    }

}
