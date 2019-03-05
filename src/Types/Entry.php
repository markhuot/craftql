<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;
use markhuot\CraftQL\Helpers\StringHelper;

class Entry extends Schema {

    protected $interfaces = [
        \markhuot\CraftQL\Types\EntryInterface::class,
        \markhuot\CraftQL\Types\ElementInterface::class,
    ];

    function boot() {
        $this->addFieldsByLayoutId($this->context['fieldLayoutId']);
    }

    function getName(): string {
        /** @var \craft\models\EntryType $entryType */
        $entryType = $this->context;
        return StringHelper::graphQLNameForEntryTypeSection($entryType['id'], $entryType['sectionId']);
    }

}