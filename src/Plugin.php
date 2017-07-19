<?php

namespace markhuot\CraftQL;

use Craft;

use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\Services\GraphQLService;
use markhuot\CraftQL\Services\SchemaAssetSourceService;
use markhuot\CraftQL\Services\SchemaCategoryGroupService;
use markhuot\CraftQL\Services\SchemaElementService;
use markhuot\CraftQL\Services\SchemaTagGroupService;
use markhuot\CraftQL\Services\RequestService;
use markhuot\CraftQL\Services\FieldService;

class Plugin extends BasePlugin
{
    public $schemaVersion = '1.0.1';
    public $controllerNamespace = 'markhuot\\CraftQL\\Controllers';
    public $hasCpSettings = true;

    function __construct($id, $parent = null, array $config = []) {
        // Make some of the services singletons
        // \Craft::$container->setSingleton(\markhuot\CraftQL\Repositories\CategoryGroup::class);
        // var_dump('a');

        parent::__construct($id, $parent, $config);
    }

    /**
     * Init for the entire plugin
     *
     * @return void
     */
    function init() {
        // var_dump('0');
        \Craft::$container->setSingleton(\markhuot\CraftQL\Repositories\CategoryGroup::class);

        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'markhuot\CraftQL\Console';
        }

        // Register cp routes
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['craftql/token-gen'] = 'craftql/cp/tokengenerate';
                $event->rules['craftql/token-del/<tokenId:\d+>'] = 'craftql/cp/tokendelete';
            }
        );

        // Register our site routes
        $uri = $this->settings->uri;
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) use ($uri) {
                $event->rules["POST {$uri}"] = 'craftql/api/index';
                $event->rules["GET {$uri}"] = 'craftql/api/graphiql';
            }
        );
    }

    /**
     * Settings Model
     *
     * @return craft\base\Model
     */
    protected function createSettingsModel()
    {
        return new Models\Settings();
    }

    /**
     * Settings HTML
     *
     * @return string
     */
    protected function settingsHtml()
    {
        return \Craft::$app->getView()->renderTemplate('craftql/settings', [
            'settings' => $this->getSettings()
        ]);
    }

    /**
     * Save the settings, some custom work here to make sure token names
     * are saved correctly
     */
    public function setSettings(array $settings)
    {
        parent::setSettings($settings);

        if (isset($_POST['settings']['token'])) {
            foreach ($_POST['settings']['token'] as $tokenId => $values) {
                $token = Token::find()->where(['id' => $tokenId])->one();
                $token->name = @$values['name'];
                $token->isWritable = @$values['isWritable'];
                $token->save();
            }
        }
    }
}
