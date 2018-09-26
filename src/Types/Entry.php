<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;
use markhuot\CraftQL\Helpers\StringHelper;

class Entry extends Schema {

    protected $interfaces = [
        \markhuot\CraftQL\Types\EntryInterface::class,
//        \markhuot\CraftQL\Types\ElementInterface::class,
    ];

    function boot() {
//        \Yii::beginProfile($this->getName(), 'bootEntry');
        $this->addFieldsByLayoutId($this->context->fieldLayoutId);
//        \Yii::endProfile($this->getName(), 'bootEntry');
    }

    function getName(): string {
        return StringHelper::graphQLNameForEntryType($this->context);
    }

}