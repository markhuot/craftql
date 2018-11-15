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

    // function boot() {
    //     $this->addIntField('id')->nonNull();
    //
    //     if ($this->request->token()->can('query:entry.author')) {
    //         $this->addField('author')->type(User::class)->nonNull();
    //     }
    //
    //     $this->addStringField('title')->nonNull();
    //     $this->addStringField('slug')->nonNull();
    //     $this->addDateField('dateCreated')->nonNull();
    //     $this->addDateField('dateUpdated')->nonNull();
    //     $this->addDateField('expiryDate');
    //     $this->addDateField('postDate');
    //     $this->addBooleanField('enabled')->nonNull();
    //     $this->addStringField('status')->nonNull();
    //     $this->addStringField('uri');
    //     $this->addStringField('url');
    //
    //     if ($this->request->token()->can('query:sections')) {
    //         $this->addField('section')->type(Section::class);
    //         $this->addField('type')->type(EntryType::class);
    //     }
    //
    //     $this->addField('ancestors')->lists()->type(EntryInterface::class);
    //
    //     $this->addField('children')
    //         ->lists()
    //         ->type(EntryInterface::class)
    //         ->use(new EntryQueryArguments);
    //
    //     $this->addField('descendants')->lists()->type(EntryInterface::class);
    //     $this->addBooleanField('hasDescendants')->nonNull();
    //     $this->addIntField('level');
    //     $this->addField('parent')->type(EntryInterface::class);
    //     $this->addField('siblings')->lists()->type(EntryInterface::class);
    // }

    static function craftQLResolveType(Entry $entry) {
        return StringHelper::graphQLNameForEntryType($entry->type);
    }

}