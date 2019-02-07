<?php

namespace markhuot\CraftQL\Types;

use markhuot\CraftQL\Builders\InterfaceBuilder;
use markhuot\CraftQL\FieldBehaviors\EntryQueryArguments;
use markhuot\CraftQL\Helpers\StringHelper;
use Craft;

class EntryInterface extends InterfaceBuilder {

    function boot() {
        $this->addIntField('id')->nonNull();

        if ($this->request->token()->can('query:entry.author')) {
            $this->addField('author')->type(User::class)->nonNull();
        }

        $this->addStringField('title')->nonNull();
        $this->addStringField('slug')->nonNull();
        $this->addDateField('dateCreated')->nonNull();
        $this->addDateField('dateUpdated')->nonNull();
        $this->addDateField('expiryDate');
        $this->addDateField('postDate');
        $this->addBooleanField('enabled')->nonNull();
        $this->addStringField('status')->nonNull();
        $this->addStringField('uri');
        $this->addStringField('url');

        $this->addStringField('fullUri')
            ->resolve(function($root, $args) {
                $site = Craft::$app->sites->getSiteById($root->siteId);
                return $root->uri ? rtrim(
                    str_replace(
                        '__home__',
                        '',
                        str_replace(
                            '@web',
                            '',
                            $site->baseUrl . $root->uri
                        )
                    ),
                    '/'
                ) : null;
            });
        
        $this->addField('site')->type(Site::class);

        $this->addField('supportedSites')->lists()->type(Site::class)
            ->resolve(function ($root, $args) {
                return array_map(function ($site) {
                    return Craft::$app->sites->getSiteById($site['siteId']);
                }, $root['supportedSites']);
            });

        $this->addField('alternateEntries')->lists()->type(EntryInterfaceAlternate::class)
            ->resolve(function ($root, $args) {
                return array_map(function ($site) use ($root) {
                    $entry = Craft::$app->entries->getEntryById($root->id, $site['siteId']);
                    return (object) [
                        'entry' => $entry,
                        'isSelf' => $root->siteId == $entry->siteId
                    ];
                }, $root['supportedSites']);
                // return array_filter($allEntries, function($entry) {
                //     return $entry->enabled;
                // });
            });

        $this->addStringField('language')
            ->resolve(function($root, $args) {
                $site = Craft::$app->sites->getSiteById($root->siteId);
                return $site->language;
            });

        if ($this->request->token()->can('query:sections')) {
            $this->addField('section')->type(Section::class);
            $this->addField('type')->type(EntryType::class);
        }

        $this->addField('ancestors')->lists()->type(EntryInterface::class);
        
        $this->addField('children')
            ->lists()
            ->type(EntryInterface::class)
            ->use(new EntryQueryArguments)
            ->resolve(function ($root, $args, $context, $info) {
                return $this->request->entries($root->{$info->fieldName}, $root, $args, $context, $info);
            });

        $this->addField('descendants')->lists()->type(EntryInterface::class);
        $this->addBooleanField('hasDescendants')->nonNull();
        $this->addIntField('level');
        $this->addField('parent')->type(EntryInterface::class);
        $this->addField('siblings')->lists()->type(EntryInterface::class);
    }

    function getResolveType() {
        return function ($entry) {
            return StringHelper::graphQLNameForEntryType($entry->type);
        };
    }

}