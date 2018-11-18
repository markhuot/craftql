<?php

namespace markhuot\CraftQL\Types;

class SectionSiteSettings {

    /**
     * @var int
     * @craftql-nonNull
     */
    public $id;

    /**
     * @var int
     * @craftql-nonNull
     */
    public $siteId;

    /**
     * @var bool
     * @craftql-nonNull
     */
    public $enabledByDefault;

    /**
     * @var bool
     * @craftql-nonNull
     */
    public $hasUrls;

    /**
     * @var string
     */
    public $uriFormat;

    /**
     * @var string
     * @craftql-nonNull
     */
    public $template;

}