<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\InterfaceType;
use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;

/**
 * Trait CategoryInterface
 * @package markhuot\CraftQL\Types
 * @craftql-type interface
 */
trait CategoryInterface {

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
     * @var string
     */
    public $uri;

    /**
     * @var int
     */
    public $level;

    /**
     * @var CategoryGroup
     */
    public $group;

    /**
     * @var CategoryInterface[]
     */
    public $children;

    /**
     * @var CategoryConnection
     */
    public $childrenConnection;

    /**
     * @var CategoryInterface
     */
    public $parent;

    /**
     * @var CategoryInterface
     */
    public $next;

    /**
     * @var CategoryInterface
     */
    public $nextSibling;

    /**
     * @var CategoryInterface
     */
    public $prev;

    /**
     * @var CategoryInterface
     */
    public $prevSibling;

    /**
     * Get the GraphQL type for the passed Craft category
     *
     * @return string
     */
    static function craftQLResolveType(\craft\elements\Category $category) {
        return ucfirst($category->group->handle).'Category';
    }

}