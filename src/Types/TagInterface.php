<?php

namespace markhuot\CraftQL\Types;

/**
 * Trait TagInterface
 * @package markhuot\CraftQL\Types
 * @craftql-type interface
 */
trait TagInterface {

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
     * @var TagGroup
     */
    public $group;

    /**
     * Get the GraphQL type for the passed Craft Tag
     *
     * @param \craft\elements\Tag $tag
     * @return string
     */
    static function craftQLResolveType(\craft\elements\Tag $tag) {
        return ucfirst($tag->group->handle).'Tags';
    }

}