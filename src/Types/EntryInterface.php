<?php

namespace markhuot\CraftQL\Types;

use craft\elements\Entry;
use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\Helpers\StringHelper;

/**
 * Class EntryInterface
 * @package markhuot\CraftQL\Types
 * @craftql-type interface
 */
trait EntryInterface {

    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $title;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var Timestamp
     */
    public $dateCreated;

    /**
     * @var Timestamp
     */
    public $dateUpdated;

    /**
     * @var Timestamp
     */
    public $expiryDate;

    /**
     * @var Timestamp
     */
    public $postDate;

    /**
     * @var bool
     */
    public $enabled;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $uri;

    /**
     * @var string
     */
    public $url;

    /**
     * @var Section
     */
    public $section;

    /**
     * @var EntryType
     */
    public $type;

    /**
     * @var EntryInterface[]
     */
    public $ancestors;

    /**
     * @var EntryInterface[]
     */
    public $children;

    /**
     * @var EntryInterface[]
     */
    public $descendants;

    /**
     * @var bool
     */
    public $hasDescendants;

    /**
     * @var int
     */
    public $level;

    /**
     * @var EntryInterface
     */
    public $parent;

    /**
     * @var EntryInterface
     */
    public $siblings;

    static function craftQLResolveType(Entry $entry) {
        return StringHelper::graphQLNameForEntryType($entry->type);
    }

}