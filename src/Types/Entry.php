<?php

namespace markhuot\CraftQL\Types;

class Entry extends ProxyObject {

    use EntryInterface;

    /**
     * @TODO revisit if this is necessary, we're setting permissions on the $request->entries so that's good, but is it necessary since it's asking for children and we probably want to return all the children anyway
     */
    function getCraftQLChildren(Request $request, \craft\elements\Entry $entry, $args, $context, ResolveInfo $info) {
        return $request->entries($entry->{$info->fieldName}, $entry, $args, $context, $info);
    }

    function getDateCreated() {
        return (int)$this->source->dateCreated->format('U');
    }

    function getDateUpdated() {
        return (int)$this->source->dateUpdated->format('U');
    }

    function getExpiryDate() {
        return (int)$this->source->expiryDate->format('U');
    }

    function getPostDate() {
        return (int)$this->source->postDate->format('U');
    }

}