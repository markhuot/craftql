<?php

namespace markhuot\CraftQL\Listeners;

use craft\fields\data\ColorData;
use GraphQL\Type\Definition\ResolveInfo;

class GetColorFieldSchema
{
    /**
     * Handle the request for the schema
     *
     * @param \markhuot\CraftQL\Events\GetFieldSchema $event
     * @return void
     */
    function handle($event) {
        $event->handled = true;

        $color = $event->schema->createObjectType('Color');
        $color->addStringField('hex')->resolve(function (ColorData $root) { return $root->getHex(); });
        $color->addStringField('rgb')->resolve(function (ColorData $root) { return $root->getRgb(); });
        $color->addFloatField('luma')->resolve(function (ColorData $root) { return $root->getLuma(); });
        $color->addIntField('r')->resolve(function (ColorData $root) { return $root->getR(); });;
        $color->addIntField('g')->resolve(function (ColorData $root) { return $root->getG(); });;
        $color->addIntField('b')->resolve(function (ColorData $root) { return $root->getB(); });;

        $event->schema->addStringField($event->sender)
            ->type($color)
            ->resolve(function ($root, $args, $context, ResolveInfo $info) {
                return $root->{$info->fieldName};
            });

        $event->mutation->addStringArgument($event->sender);
    }
}
