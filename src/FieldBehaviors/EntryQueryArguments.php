<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class EntryQueryArguments extends Behavior {

    function initEntryQueryArguments() {
        $this->owner->addStringArgument('after');
        $this->owner->addIntArgument('ancestorOf');
        $this->owner->addIntArgument('ancestorDist');
        $this->owner->addBooleanArgument('archived');
        $this->owner->addStringArgument('authorGroup');
        $this->owner->addIntArgument('authorGroupId');
        $this->owner->addIntArgument('authorId')->lists();
        $this->owner->addStringArgument('before');
        $this->owner->addIntArgument('level');
        $this->owner->addBooleanArgument('localeEnabled');
        $this->owner->addIntArgument('descendantOf');
        $this->owner->addIntArgument('descendantDist');
        $this->owner->addBooleanArgument('fixedOrder');
        $this->owner->addIntArgument('id')->lists();
        $this->owner->addIntArgument('limit');
        $this->owner->addStringArgument('locale');
        $this->owner->addIntArgument('nextSiblingOf');
        $this->owner->addIntArgument('offset');
        $this->owner->addStringArgument('order');
        $this->owner->addIntArgument('positionedAfter');
        $this->owner->addIntArgument('positionedBefore');
        $this->owner->addStringArgument('postDate');
        $this->owner->addIntArgument('prevSiblingOf');
        // $this->owner->addStringArgument('relatedTo' => Type::listOf(static::relatedToInputObject()),
        // $this->owner->addStringArgument('orRelatedTo' => Type::listOf(static::relatedToInputObject()),
        $this->owner->addStringArgument('search');
        $this->owner->addStringArgument('section')->lists()->type($this->owner->getRequest()->sections()->enum());
        $this->owner->addIntArgument('siblingOf');
        $this->owner->addStringArgument('slug');
        $this->owner->addStringArgument('status');
        $this->owner->addStringArgument('title');
        $this->owner->addStringArgument('type')->lists()->type($this->owner->getRequest()->entryTypes()->enum());
        $this->owner->addStringArgument('uri');
    }

}