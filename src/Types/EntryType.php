<?php

namespace markhuot\CraftQL\Types;

use Craft;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use craft\models\EntryType as CraftEntryType;
use markhuot\CraftQL\Helpers\StringHelper;

class EntryType extends ProxyObject {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $handle;

    /**
     * @return string
     */
    function getGraphQlTypeName() {
        return StringHelper::graphQLNameForEntryType($this->source);
    }

    /**
     * @todo nonnull
     * @var Field[]
     */
    public $fields;

}