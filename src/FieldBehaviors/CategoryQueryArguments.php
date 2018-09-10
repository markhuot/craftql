<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;

class CategoryQueryArguments extends FieldBehavior {

    function initCategoryQueryArguments() {
        $this->owner->addIntArgument('ancestorOf');
        $this->owner->addIntArgument('ancestorDist');
        $this->owner->addIntArgument('level');
        $this->owner->addIntArgument('descendantOf');
        $this->owner->addIntArgument('descendantDist');
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addArgument('group')->type($this->owner->request->categoryGroups()->enum())->lists();
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id')->lists();
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('site');
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('nextSiblingOf');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('orderBy');
        $this->owner->addIntArgument('positionedAfter');
        $this->owner->addIntArgument('positionedBefore');
        $this->owner->addIntArgument('prevSiblingOf');
        // $this->owner->addIntArgument('relatedTo' => Type::listOf(Entry::relatedToInputObject()),
        $this->owner->addStringArgument('search');
        $this->owner->addIntArgument('siblingOf');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('title');
        $this->owner->addStringArgument('uri');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($this->owner->getRequest());
        $this->owner->addArguments($arguments, false);
    }

}
