<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\Helpers\StringHelper;

/**
 * Class EntryInterface
 * @package markhuot\CraftQL\Types
 * @craftql-type interface
 */
class EntryInterface {

    /** @var int */
    public $id;

    /** @var User */
    public $author;

    /** @var string */
    public $title;

    /** @var string */
    public $slug;

    /** @var Timestamp */
    public $dateCreated;

    /** @var Timestamp */
    public $dateUpdated;

    /** @var Timestamp */
    public $expiryDate;

    /** @var Timestamp */
    public $postDate;

    /** @var bool */
    public $enabled;

    /** @var string */
    public $status;

    /** @var string */
    public $uri;

    /** @var string */
    public $url;

    /** @var Section */
    public $section;

    /** @var EntryType */
    public $type;

    /** @var static[] */
    public $ancestors;

    /** @var static[] */
    public $children;

    /** @var static[] */
    public $descendants;

    /** @var bool */
    public $hasDescendants;

    /** @var int */
    public $level;

    /** @var static */
    public $parent;

    /** @var static[] */
    public $siblings;

    // function getResolveType() {
    //     return function ($entry) {
    //         return StringHelper::graphQLNameForEntryType($entry->type);
    //     };
    // }

}