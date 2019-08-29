<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\Schema;

class Section extends Schema {

    function boot() {
        $this->addIntField('id')->nonNull();
        $this->addIntField('structureId');
        $this->addStringField('name')->nonNull();
        $this->addStringField('handle')->nonNull();
        $this->addStringField('type')->nonNull();
        $this->addStringField('siteSettings')
            ->lists()
            ->type(SectionSiteSettings::class)
            ->resolve(function ($root, $args) {
                /** @var \craft\models\Section $root */
                return $root->getSiteSettings();
            });
        $this->addIntField('maxLevels');
        $this->addBooleanField('hasUrls');
        $this->addBooleanField('enableVersioning');
        $this->addField('entryTypes')->lists()->type(EntryType::class);
    }

}
