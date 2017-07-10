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
    public $controllerNamespace = 'markhuot\\CraftQL\\Controllers';

    function init() {
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\FieldService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\GraphQLService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\RequestService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaAssetSourceService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaCategoryGroupService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaElementService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaEntryService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaSectionService::class);
        \Yii::$container->setSingleton(\markhuot\CraftQL\Services\SchemaTagGroupService::class);

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'markhuot\CraftQL\Console';
        }

        // Register our site routes
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['POST api'] = 'craftql/api/index';
                $event->rules['GET api'] = 'craftql/api/graphiql';
            }
        );

    }
}
