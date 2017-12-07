<?php

return [
    \craft\fields\Assets::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetAssetsFieldSchema::class,
        ],
    ],
    \craft\fields\Date::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetDateFieldSchema::class,
        ],
    ],
    \craft\fields\Dropdown::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetSelectOneSchema::class,
        ],
    ],
    \craft\fields\Lightswitch::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetLightswitchFieldSchema::class,
        ],
    ],
    \craft\base\Field::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetDefaultFieldSchema::class,
        ],
    ],
];