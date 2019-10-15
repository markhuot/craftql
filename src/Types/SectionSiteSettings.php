<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class SectionSiteSettings extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addIntField('siteId')->nonNull();
        $this->addBooleanField('enabledByDefault')->nonNull();
        $this->addBooleanField('hasUrls')->nonNull();
        $this->addStringField('uriFormat');
        $this->addStringField('template')->nonNull();
    }

}
