<?php

namespace markhuot\CraftQL\Types;

class Section {

    /**
     * @todo nonnull
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $structureId;

    /**
     * @todo nonnull
     * @var string
     */
    public $name;

    /**
     * @todo nonnull
     * @var string
     */
    public $handle;

    /**
     * @todo nonnull
     * @var string
     */
    public $type;

    /**
     * @var SectionSiteSettings[]
     */
    public $siteSettings;

    /**
     * @var int
     */
    public $maxLevels;

    /**
     * @var bool
     */
    public $hasUrls;

    /**
     * @var boolean
     */
    public $enableVersioning;

    /**
     * @var EntryType[]
     */
    public $entryTypes;

}