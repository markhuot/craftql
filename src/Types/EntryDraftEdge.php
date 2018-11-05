<?php

namespace markhuot\CraftQL\Types;

// use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;

class EntryDraftEdge {

    /**
     * @var string
     */
    public $cursor = 'Not implemented';

    /**
     * @var EntryInterface
     */
    public $node;

    /**
     * @var EntryDraftInfo
     */
    public $draftInfo;

    /**
     * EntryDraftEdge constructor.
     *
     * @param \craft\elements\Entry $draft
     */
    function __construct(\craft\elements\Entry $draft) {
        $this->node = $draft;
        $this->draftInfo = new EntryDraftInfo($draft);
    }

}