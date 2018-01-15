<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\AssetTransformArguments;

class VolumeInterface extends InterfaceBuilder {

    function boot() {
        $this->addIntField('id');
        $this->addStringField('uri');
        $this->addStringField('url')
            ->use(AssetTransformArguments::class);

        $this->addStringField('width');
        $this->addStringField('height');
        $this->addIntField('size');
        $this->addStringField('folder');
        $this->addStringField('mimeType');
        $this->addStringField('title');
        $this->addStringField('extension');
        $this->addStringField('filename');
        $this->addDateField('dateCreatedTimestamp');
        $this->addDateField('dateCreated');
        $this->addDateField('dateUpdatedTimestamp');
        $this->addDateField('dateUpdated');
    }

    function getResolveType() {
        return function ($type) {
            return ucfirst($type->volume->handle).'Volume';
        };
    }

}