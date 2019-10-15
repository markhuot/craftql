<?php

namespace markhuot\CraftQL\Types;

class EntryDraftConnection extends EntryConnection {

    function boot() {
        parent::boot();

        $this->getField('edges')
            // @TODO add this in at some point, it's a breaking change though
            // ->lists()
            ->type(EntryDraftEdge::class);
    }

}
