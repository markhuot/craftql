<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class EntryQueryArguments extends Behavior {

    function initEntryQueryArguments() {
        $this->owner->arguments([
            'after' => Type::string(),
            'ancestorOf' => Type::int(),
            'ancestorDist' => Type::int(),
            'archived' => Type::boolean(),
            'authorGroup' => Type::string(),
            'authorGroupId' => Type::int(),
            'authorId' => Type::listOf(Type::int()),
            'before' => Type::string(),
            'level' => Type::int(),
            'localeEnabled' => Type::boolean(),
            'descendantOf' => Type::int(),
            'descendantDist' => Type::int(),
            'fixedOrder' => Type::boolean(),
            'id' => Type::listOf(Type::int()),
            'limit' => Type::int(),
            'locale' => Type::string(),
            'nextSiblingOf' => Type::int(),
            'offset' => Type::int(),
            'order' => Type::string(),
            'positionedAfter' => Type::id(),
            'positionedBefore' => Type::id(),
            'postDate' => Type::string(),
            'prevSiblingOf' => Type::id(),
            // 'relatedTo' => Type::listOf(static::relatedToInputObject()),
            // 'orRelatedTo' => Type::listOf(static::relatedToInputObject()),
            'search' => Type::string(),
            'section' => Type::listOf($this->owner->getRequest()->sections()->enum()),
            'siblingOf' => Type::int(),
            'slug' => Type::string(),
            'status' => Type::string(),
            'title' => Type::string(),
            'type' => Type::listOf($this->owner->getRequest()->entryTypes()->enum()),
            'uri' => Type::string(),
        ]);
    }

}