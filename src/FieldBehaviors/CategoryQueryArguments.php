<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class CategoryQueryArguments extends Behavior {

    function initCategoryQueryArguments() {
        $this->owner->addIntArgument('ancestorOf');
        $this->owner->addIntArgument('ancestorDist');
        $this->owner->addIntArgument('level');
        $this->owner->addIntArgument('descendantOf');
        $this->owner->addIntArgument('descendantDist');
        $this->owner->addBooleanArgument('fixedOrder');
        // $this->owner->addIntArgument('group' => $this->request->categoryGroups()->enum(),
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id');
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('site');
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('nextSiblingOf');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addIntArgument('positionedAfter');
        $this->owner->addIntArgument('positionedBefore');
        $this->owner->addIntArgument('prevSiblingOf');
        // $this->owner->addIntArgument('relatedTo' => Type::listOf(Entry::relatedToInputObject()),
        $this->owner->addStringArgument('search');
        $this->owner->addIntArgument('siblingOf');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('title');
        $this->owner->addStringArgument('uri');
    }

}
