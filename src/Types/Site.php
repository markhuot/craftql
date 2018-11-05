<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;

class Site {

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
     * @var string
     */
    public $baseUrl;

    /**
     * @var bool
     */
    public $hasUrls;

    /**
     * @var string
     */
    public $language;

    /**
     * @var string
     */
    public $originalBaseUrl;

    /**
     * @var string
     */
    public $originalName;

    /**
     * @var string
     */
    public $sortOrder;

    /**
     * @var bool
     */
    public $primary;

}