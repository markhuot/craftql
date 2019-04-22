<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Types\CategoryGroupsEnum;
use markhuot\CraftQL\Types\RelatedToInputType;
use markhuot\CraftQL\Types\SitesEnum;

class CategoryQueryArguments extends FieldBehavior {

    function initCategoryQueryArguments() {
        $this->owner->addIntArgument('ancestorOf');
        $this->owner->addIntArgument('ancestorDist');
        $this->owner->addIntArgument('level');
        $this->owner->addIntArgument('descendantOf');
        $this->owner->addIntArgument('descendantDist');
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addArgument('group')->type(CategoryGroupsEnum::class)->lists();
        $this->owner->addIntArgument('groupId');
        $this->owner->addIntArgument('id')->lists();
        $this->owner->addStringArgument('indexBy');
        $this->owner->addIntArgument('limit');
        $this->owner->addArgument('site')->type(SitesEnum::class);
        $this->owner->addIntArgument('siteId');
        $this->owner->addIntArgument('nextSiblingOf');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addStringArgument('orderBy');
        $this->owner->addIntArgument('positionedAfter');
        $this->owner->addIntArgument('positionedBefore');
        $this->owner->addIntArgument('prevSiblingOf');
        $this->owner->addArgument('relatedTo')->type(RelatedToInputType::class)->lists();
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
