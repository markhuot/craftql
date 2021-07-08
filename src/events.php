<?php

return [
    \craft\fields\Assets::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetAssetsFieldSchema,
        ],
    ],
    \craft\fields\Categories::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetCategoriesFieldSchema,
        ],
    ],
    \craft\fields\Checkboxes::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSelectMultipleFieldSchema,
        ],
    ],
    \craft\fields\Color::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetColorFieldSchema,
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
    \craft\fields\RadioButtons::class => [
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
    \craft\fields\Matrix::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetMatrixFieldSchema,
        ],
    ],
    \craft\fields\MultiSelect::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSelectMultipleFieldSchema,
        ],
    ],
    \craft\fields\Number::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetNumberFieldSchema,
        ],
    ],
    \craft\redactor\Field::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetRedactorFieldSchema,
        ],
    ],
    \verbb\supertable\fields\SuperTableField::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetSuperTableFieldSchema,
        ],
    ],
    \plugins\dolphiq\iconpicker\fields\Iconpicker::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetIconPickerFieldSchema,
        ],
    ],
    \craft\fields\Table::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetTableFieldSchema,
        ],
    ],
    \craft\fields\Tags::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetTagsFieldSchema,
        ],
    ],
    \craft\fields\Users::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetUsersFieldSchema,
        ],
    ],
    \dukt\videos\fields\Video::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetVideosFieldSchema,
        ],
    ],
    \craft\base\Field::class => [
        'craftQlGetFieldSchema' => [
            new \markhuot\CraftQL\Listeners\GetDefaultFieldSchema,
        ],
    ],
];