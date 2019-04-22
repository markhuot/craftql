<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\CraftQL;

class SitesEnum extends EnumObject {

    function getValues() {
        $token = $this->token;

        return array_map(function ($site) {
            return $site['handle'];
        }, array_filter(CraftQL::$plugin->sites->all(), function ($site) use ($token) {
            return $token->can('query:entryType:'.$site['id']);
        }));
    }

}