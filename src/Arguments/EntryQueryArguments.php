<?php

namespace markhuot\CraftQL\Arguments;

use markhuot\CraftQL\Types\RelatedToInputType;

class EntryQueryArguments {

    /**
     * @var string
     */
    public $after;

    /**
     * @var int
     */
    public $ancestorOf;

    /**
     * @var int
     */
    public $ancestorDist;

    /**
     * @var bool
     */
    public $archived;

    /**
     * @var string
     */
    public $authorGroup;

    /**
     * @var int
     */
    public $authorGroupId;

    /**
     * @var int[]
     */
    public $authorId;

    /**
     * @var string
     */
    public $before;

    /**
     * @var int
     */
    public $level;

    /**
     * @var boolean
     */
    public $localeEnabled;

    /**
     * @var int
     */
    public $descendantOf;

    /**
     * @var int
     */
    public $descendantDist;

    /**
     * @var boolean
     */
    public $fixedOrder;

    /**
     * @var int
     */
    public $id;

    /**
     * @var int[]
     */
    public $idNot;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var string
     */
    public $site;

    /**
     * @var int
     */
    public $siteId;

    /**
     * @var int
     */
    public $nextSiblingOf;

    /**
     * @var int
     */
    public $offset;

    /**
     * @var string
     */
    public $order;

    /**
     * @var string
     */
    public $orderBy;

    /**
     * @var int
     */
    public $positionedAfter;

    /**
     * @var int
     */
    public $positionedBefore;

    /**
     * @var string
     */
    public $postDate;

    /**
     * @var int
     */
    public $prevSiblingOf;

    /**
     * @var RelatedToInputType[]
     */
    public $relatedTo;

    /**
     * @var RelatedToInputType[]
     */
    public $orRelatedTo;

    /**
     * @var string
     */
    public $search;

    // /**
    //  * @var string[]
    //  */
    // public $section; //type($this->owner->getRequest()->sections()->enum());

    /**
     * @var int
     */
    public $siblingOf;

    /**
     * @var string
     */
    public $slug;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $title;

    // /**
    //  * @var string[]
    //  */
    // public $type; //type($this->owner->getRequest()->entryTypes()->enum());

    /**
     * @var string
     */
    public $uri;


}