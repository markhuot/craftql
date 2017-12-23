<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\AssetTransformArguments;

class VolumeInterface extends Schema {

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

    function getGraphQLObject() {
        return new InterfaceType($this->getConfig());
    }

    function getResolveType() {
        return function ($type) {
            return ucfirst($type->volume->handle).'Volume';
        };
    }

}