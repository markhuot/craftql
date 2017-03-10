<?php

namespace Craft;

class CraftQLPlugin extends BasePlugin
{
    function getName()
    {
         return Craft::t('Craft QL');
    }

    function getVersion()
    {
        return '1.0';
    }

    function getDeveloper()
    {
        return 'Mark Huot';
    }

    function getDeveloperUrl()
    {
        return 'http://markhuot.com';
    }

    function registerSiteRoutes()
    {
        return [
            'api' => ['action' => 'CraftQL/query/query'],
        ];
    }
}
