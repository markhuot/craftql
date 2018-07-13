<?php

namespace markhuot\CraftQL;

use Craft;

use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use markhuot\CraftQL\Services\GraphQLService;
use markhuot\CraftQL\Services\JWTService;
use markhuot\CraftQL\Models\Settings;
use yii\base\Event;

use markhuot\CraftQL\Models\Token;

/**
 * Class CraftQL
 * @package markhuot\CraftQL
 * @property JWTService jwt
 * @property GraphQLService graphQl
 */
class CraftQL extends Plugin
{
    // const EVENT_GET_FIELD_SCHEMA = 'getFieldSchema';

    public $schemaVersion = '1.1.0';
    public $controllerNamespace = 'markhuot\\CraftQL\\Controllers';
    public $hasCpSettings = true;
    public $hasCpSection = true;

    /**
     * Init for the entire plugin
     *
     * @return void
     */
    function init() {
        // Add in our console commands
        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'markhuot\CraftQL\Console';
        }

        // make sure there's only one instance of our field service
        Craft::$container->setSingleton('craftQLFieldService', \markhuot\CraftQL\Services\FieldService::class);

        // Register cp routes
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_CP_URL_RULES,
            function (RegisterUrlRulesEvent $event) {
                $event->rules['GET craftql'] = 'craftql/cp/index';
                $event->rules['GET craftql/token-gen'] = 'craftql/cp/tokengenerate';
                $event->rules['GET craftql/token-del/<tokenId:\d+>'] = 'craftql/cp/tokendelete';
                $event->rules['GET craftql/token/<tokenId:\d+>/scopes'] = 'craftql/cp/tokenscopes';
                $event->rules['POST craftql/token/<tokenId:\d+>/scopes'] = 'craftql/cp/savetokenscopes';
                $event->rules['GET craftql/browse'] = 'craftql/cp/graphiql';
                $event->rules['GET craftql/browse/<token:.+>'] = 'craftql/cp/graphiqlas';
            }
        );

        // Register our site routes
        $verbs = $this->getSettings()->verbs;
        $uri = $this->settings->uri;
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) use ($uri, $verbs) {
                $event->rules["OPTIONS {$uri}"] = 'craftql/api/index';
                foreach ($verbs as $verb) {
                    $event->rules["{$verb} {$uri}"] = 'craftql/api/index';
                }
                $event->rules["GET {$uri}/debug"] = 'craftql/api/debug';
            }
        );

        // register our events and listeners
        foreach (require('events.php') as $class => $events) {
            foreach ($events as $eventName => $listeners) {
                foreach ($listeners as $listener) {
                    if (is_array($listener)) {
                        Event::on($class, $eventName, $listener);
                    }
                    else {
                        Event::on($class, $eventName, [$listener, 'handle']);
                    }
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public static function getInstance(): self {
        return parent::getInstance();
    }

    public function getSettings(): Settings {
        return parent::getSettings();
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
                $token->save();
            }
        }
    }
}
