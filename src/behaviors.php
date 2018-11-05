<?php

return [
    \craft\models\EntryType::class => [
        \markhuot\CraftQL\Behaviors\EntryType::class,
    ],
    \craft\elements\User::class => [
        \markhuot\CraftQL\Behaviors\User::class,
    ],
    \craft\elements\Entry::class => [
        \markhuot\CraftQL\Behaviors\Entry::class,
    ],
    \craft\base\Field::class => [
        \markhuot\CraftQL\Behaviors\Field::class,
    ],
    \craft\elements\Asset::class => [
        \markhuot\CraftQL\Behaviors\Asset::class,
    ],
];