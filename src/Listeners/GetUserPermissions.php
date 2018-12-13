<?php

namespace markhuot\CraftQL\Listeners;

use Craft;
use craft\events\RegisterUserPermissionsEvent;

class GetUserPermissions
{
    /**
     * Handle the request for the schema
     */
    function handle(RegisterUserPermissionsEvent $event) {
        $queryTypes = [];
        $mutationTypes = [];
        $sections = Craft::$app->sections->getAllSections();
        foreach ($sections as $section) {
            $entryTypes = $section->getEntryTypes();
            foreach ($entryTypes as $entryType) {
                $id = $entryType->id;
                $queryTypes["craftql:query:entrytype:{$id}"] = ['label' => \Craft::t('craftql', 'Query their own entries of the '.$entryType->name.' entry type')];
                $queryTypes["craftql:query:entrytype:{$id}:all"] = ['label' => \Craft::t('craftql', 'Query all entries of the '.$entryType->name.' entry type')];
                $mutationTypes["craftql:mutate:entrytype:{$id}"] = ['label' => \Craft::t('craftql', 'Mutate their own entries of the '.$entryType->name.' entry type')];
            }
        }
        $queryTypes['craftql:query:otheruserentries'] = ['label' => \Craft::t('craftql', 'Query other authors’ entries')];

        $event->permissions[\Craft::t('craftql', 'CraftQL Queries')] = [
            'craftql:query:entries' => ['label' => \Craft::t('craftql', 'Query Entries'), 'nested' => $queryTypes],
            'craftql:query:entry.author' => ['label' => \Craft::t('craftql', 'Query Entry Authors')],
            'craftql:query:globals' => ['label' => \Craft::t('craftql', 'Query Globals')],
            'craftql:query:categories' => ['label' => \Craft::t('craftql', 'Query Categories')],
            'craftql:query:tags' => ['label' => \Craft::t('craftql', 'Query Tags')],
            'craftql:query:users' => ['label' => \Craft::t('craftql', 'Query Users')],
            'craftql:query:sections' => ['label' => \Craft::t('craftql', 'Query Sections')],
            'craftql:query:fields' => ['label' => \Craft::t('craftql', 'Query Fields')],
            'craftql:mutate:entries' => ['label' => \Craft::t('craftql', 'Mutate Entries'), 'nested' => $mutationTypes],
            'craftql:mutate:globals' => ['label' => \Craft::t('craftql', 'Mutate Entries')],
        ];
    }
}
