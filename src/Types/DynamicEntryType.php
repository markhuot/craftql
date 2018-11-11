<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\GraphQLFields\General\Date as DateField;
use markhuot\CraftQL\Helpers\StringHelper;

/**
 * Class DynamicEntryType
 */
class DynamicEntryType {

    use EntryInterface;

    // protected $interfaces = [
    //     \markhuot\CraftQL\Types\EntryInterface::class,
    //     \markhuot\CraftQL\Types\ElementInterface::class,
    // ];
    //
    // function boot() {
    //     $this->addFieldsByLayoutId($this->context->fieldLayoutId);
    // }
    //
    // function getName(): string {
    //     return StringHelper::graphQLNameForEntryType($this->context);
    // }

}