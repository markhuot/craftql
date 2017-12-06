<?php

return [
    \craft\fields\Dropdown::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetSelectOneSchema::class,
        ],
    ],
    \craft\base\Field::class => [
        'craftQlGetFieldSchema' => [
            \markhuot\CraftQL\Listeners\GetDefaultFieldSchema::class,
        ],
    ],
];