<?php

namespace markhuot\CraftQL\FieldBehaviors;

use yii\base\Behavior;
use GraphQL\Type\Definition\Type;

class EntryMutationArguments extends Behavior {

    function initEntryMutationArguments() {
        $this->owner->arguments([
            'id' => Type::int(),
            'authorId' => Type::int(),
            'title' => Type::string(),
        ]);
    }

}