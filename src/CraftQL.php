<?php

namespace markhuot\CraftQL;

use Craft;

use craft\base\Plugin;
use craft\console\Application as ConsoleApplication;
use craft\models\Section;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use markhuot\CraftQL\Models\Token;

use craft\events\RegisterUserPermissionsEvent;
use craft\services\UserPermissions;

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

        // register our permissions
        Event::on(UserPermissions::class, UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $queryTypes = [];
            $mutationTypes = [];
            $sections = Craft::$app->sections->getAllSections();
            foreach ($sections as $section) {
                $entryTypes = $section->getEntryTypes();
                foreach ($entryTypes as $entryType) {
                    $id = $entryType->id;
                    $queryTypes["craftql:query:entrytype:{$id}"] = ['label' => \Craft::t('craftql', 'Query their own entries of the '.$entryType->name.' entry type')];
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
        });
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
