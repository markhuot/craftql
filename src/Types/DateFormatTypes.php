<?php

namespace markhuot\CraftQL\Types;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\FieldBehaviors\CategoryQueryArguments;
use markhuot\CraftQL\Request;
use markhuot\CraftQL\Builders\Schema;
use markhuot\CraftQL\FieldBehaviors\RelatedEntriesField;

class DateFormatTypes extends EnumObject {

    function getValues() {
        return [
            'atom' => ['description' => 'Atom feeds'],
            'cookie' => ['description' => 'HTTP cookies'],
            'iso8601' => ['description' => 'ISO-8601 spec'],
            'rfc822' => ['description' => 'RFC-822 spec'],
            'rfc850' => ['description' => 'RFC-850 spec'],
            'rfc1036' => ['description' => 'RFC-1036 spec'],
            'rfc1123' => ['description' => 'RFC-1123 spec'],
            'rfc2822' => ['description' => 'RFC-2822 spec'],
            'rfc3339' => ['description' => 'RFC-3339 spec'],
            'rss' => ['description' => 'RSS feed'],
            'w3c' => ['description' => 'W3C spec'],
        ];
    }

}