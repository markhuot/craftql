<?php

namespace markhuot\CraftQL\Behaviors;

use Craft;
use GraphQL\Type\Definition\ResolveInfo;
use markhuot\CraftQL\Helpers\StringHelper;
use yii\base\Behavior;

class EntryType extends Behavior {

    public function getCraftQLGraphQlTypeName(\craft\models\EntryType $entryType, $args, $context, ResolveInfo $info) {
        return StringHelper::graphQLNameForEntryType($entryType);
    }

}