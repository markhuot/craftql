<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class EntryDraftConnection {

    /**
     * @var \craft\elements\Entry[]
     */
    private $drafts;

    /**
     * EntryDraftConnection constructor.
     *
     * @TODO add back in pageinfo to entrydraftconnection
     * @param $drafts \craft\elements\Entry[]
     */
    function __construct($drafts) {
        $this->drafts = $drafts;
    }

    /**
     * @return EntryDraftEdge[]
     */
    function getEdges() {
        return array_map(function ($draft) {
            return new EntryDraftEdge($draft);
        }, $this->drafts);
    }

}