<?php

namespace markhuot\CraftQL;

use Craft;

use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use markhuot\CraftQL\Services\GraphQLService;
use markhuot\CraftQL\Services\SchemaAssetSourceService;
use markhuot\CraftQL\Services\SchemaCategoryGroupService;
use markhuot\CraftQL\Services\SchemaElementService;
use markhuot\CraftQL\Services\SchemaEntryService;
use markhuot\CraftQL\Services\SchemaSectionService;
use markhuot\CraftQL\Services\SchemaTagGroupService;
use markhuot\CraftQL\Services\RequestService;
use markhuot\CraftQL\Services\FieldService;

class Plugin extends BasePlugin
{
    static $plugin;
    static $graphQLService;
    static $schemaAssetSourceService;
    static $schemaCategoryGroupService;
    static $schemaElementService;
    static $schemaEntryService;
    static $schemaSectionService;
    static $schemaTagGroupService;
    static $requestService;
    static $fieldService;

    public $controllerNamespace = 'markhuot\\CraftQL\\Controllers';

    function init() {
        self::$plugin = $this;
        self::$graphQLService = new GraphQLService;
        self::$schemaAssetSourceService = new SchemaAssetSourceService;
        self::$schemaCategoryGroupService = new SchemaCategoryGroupService;
        self::$schemaElementService = new SchemaElementService;
        self::$schemaEntryService = new SchemaEntryService;
        self::$schemaSectionService = new SchemaSectionService;
        self::$schemaTagGroupService = new SchemaTagGroupService;
        self::$requestService = new RequestService;
        self::$fieldService = new FieldService;

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'markhuot\CraftQL\Console';
        }

        // Register our site routes
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['api'] = 'craftql/api';
            }
        );

    }
}
