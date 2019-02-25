<?php

namespace markhuot\CraftQL\FieldBehaviors;

use markhuot\CraftQL\Behaviors\FieldBehavior;
use markhuot\CraftQL\Builders\Field;
use markhuot\CraftQL\Builders\InputSchema;

class EntryQueryArguments extends FieldBehavior {

    static $args = null;
    static $inputObjectType;

    function initEntryQueryArguments() {
        if (static::$args !== null) {
            $this->owner->addArguments(static::$args, false);
            return;
        }

        $tmp = new Field($this->owner->request, 'TmpHoldingForAllQueryArgs');

        $tmp->addStringArgument('after');
        $tmp->addIntArgument('ancestorDist');
        $tmp->addIntArgument('ancestorOf');
        $tmp->addBooleanArgument('anyStatus');
        $tmp->addBooleanArgument('archived');
        $tmp->addStringArgument('authorGroup')->lists();
        $tmp->addIntArgument('authorGroupId')->lists();
        $tmp->addIntArgument('authorId')->lists();
        $tmp->addStringArgument('before');
        $tmp->addStringArgument('dateCreated');
        $tmp->addStringArgument('dateUpdated');
        $tmp->addIntArgument('descendantDist');
        $tmp->addIntArgument('descendantOf');
        $tmp->addBooleanArgument('enabledForSite');
        $tmp->addStringArgument('expiryDate');
        $tmp->addBooleanArgument('fixedOrder');
        $tmp->addBooleanArgument('hasDescendants');
        // $tmp->addBooleanArgument('localeEnabled');
        $tmp->addIntArgument('id')->lists();
        $tmp->addIntArgument('idNot')->lists();
        $tmp->addBooleanArgument('inReverse');
        $tmp->addBooleanArgument('leaves');
        $tmp->addIntArgument('level');
        $tmp->addIntArgument('limit');
        $tmp->addIntArgument('nextSiblingOf');
        $tmp->addIntArgument('offset');
        $tmp->addStringArgument('order');
        $tmp->addStringArgument('orderBy');
        $tmp->addIntArgument('positionedAfter');
        $tmp->addIntArgument('positionedBefore');
        $tmp->addStringArgument('postDate');
        $tmp->addIntArgument('prevSiblingOf');
        $tmp->addStringArgument('relatedTo')->lists()->type($this->relatedToInputObject());
        $tmp->addStringArgument('orRelatedTo')->lists()->type($this->relatedToInputObject());
        $tmp->addStringArgument('search');
        // $tmp->addStringArgument('section')->lists()->type($this->owner->getRequest()->sections()->enum());
        $tmp->addIntArgument('siblingOf');
        // $tmp->addArgument('site')->type($this->owner->getRequest()->sites()->enum());
        $tmp->addIntArgument('siteId');
        $tmp->addStringArgument('slug')->lists();
        $tmp->addStringArgument('status')->lists();
        $tmp->addStringArgument('title')->lists();
        $tmp->addBooleanArgument('trashed');
        // $tmp->addStringArgument('type')->lists()->type($this->owner->getRequest()->entryTypes()->enum());
        $tmp->addStringArgument('uid');
        $tmp->addStringArgument('uri');

        $fieldService = \Yii::$container->get('craftQLFieldService');
        $arguments = $fieldService->getQueryArguments($tmp->getRequest());
        $tmp->addArguments($arguments, false);
        static::$args = $tmp->getArguments();

        $this->owner->addArguments(static::$args, false);
        return static::$args;
    }

    function relatedToInputObject() {
        if (!empty(static::$inputObjectType)) {
            return static::$inputObjectType;
        }

        $type = $this->owner->createInputObjectType('RelatedToInputType');
        $type->addIntArgument('element')->lists();
        $type->addBooleanArgument('elementIsEdge');
        $type->addIntArgument('sourceElement')->lists();
        $type->addBooleanArgument('sourceElementIsEdge');
        $type->addIntArgument('targetElement')->lists();
        $type->addBooleanArgument('targetElementIsEdge');
        $type->addStringArgument('field');
        $type->addStringArgument('sourceLocale');
        return static::$inputObjectType = $type;
    }

}