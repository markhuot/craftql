<?php

return [
    \craft\fields\Assets::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetAssetsFieldSchema,
        ],
    ],
    \craft\fields\Checkboxes::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSelectMultipleFieldSchema,
        ],
    ],
    \craft\fields\Date::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetDateFieldSchema,
        ],
    ],
    \craft\fields\Dropdown::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSelectOneFieldSchema,
        ],
    ],
    \craft\fields\Entries::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetEntriesFieldSchema,
        ],
    ],
    \craft\fields\Lightswitch::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetLightswitchFieldSchema,
        ],
    ],
    \craft\fields\MultiSelect::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSelectMultipleFieldSchema,
        ],
    ],
    \craft\base\Field::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetDefaultFieldSchema,
        ],
    ],
];