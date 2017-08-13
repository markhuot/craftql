<?php

namespace markhuot\CraftQL;

use Craft;

use craft\base\Plugin as BasePlugin;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

use markhuot\CraftQL\Models\Token;

class Plugin extends BasePlugin
{
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
        $uri = $this->settings->uri;
        Event::on(
            UrlManager::className(),
            UrlManager::EVENT_REGISTER_SITE_URL_RULES,
            function (RegisterUrlRulesEvent $event) use ($uri) {
                $event->rules["POST {$uri}"] = 'craftql/api/index';
            }
        );

        $mappings = [
            \craft\fields\RichText::class => \markhuot\CraftQL\Fields\RichTextBehavior::class,
            \craft\fields\Lightswitch::class => \markhuot\CraftQL\Fields\LightswitchBehavior::class,
            \craft\fields\Date::class => \markhuot\CraftQL\Fields\DateBehavior::class,
            \craft\fields\Checkboxes::class => \markhuot\CraftQL\Fields\SelectMultipleBehavior::class,
            \craft\fields\MultiSelect::class => \markhuot\CraftQL\Fields\SelectMultipleBehavior::class,
            \craft\fields\Categories::class => \markhuot\CraftQL\Fields\CategoriesBehavior::class,
            \craft\fields\PositionSelect::class => \markhuot\CraftQL\Fields\PositionSelectBehavior::class,
            \craft\fields\Entries::class => \markhuot\CraftQL\Fields\EntriesBehavior::class,
            \craft\fields\Number::class => \markhuot\CraftQL\Fields\NumberBehavior::class,
            \craft\fields\RadioButtons::class => \markhuot\CraftQL\Fields\SelectOneBehavior::class,
            \craft\fields\Dropdown::class => \markhuot\CraftQL\Fields\SelectOneBehavior::class,
            \craft\fields\Assets::class => \markhuot\CraftQL\Fields\AssetsBehavior::class,
            \craft\fields\Matrix::class => \markhuot\CraftQL\Fields\MatrixBehavior::class,
            \craft\fields\Table::class => \markhuot\CraftQL\Fields\TableBehavior::class,
            \craft\fields\Tags::class => \markhuot\CraftQL\Fields\TagsBehavior::class,
            \craft\fields\Color::class => \markhuot\CraftQL\Fields\DefaultBehavior::class,
            \craft\fields\PlainText::class => \markhuot\CraftQL\Fields\DefaultBehavior::class,
        ];

        // Register monkeypatching
        foreach ($mappings as $fieldClass => $behaviorClass) {
            Event::on($fieldClass, $fieldClass::EVENT_INIT, function ($event) use ($behaviorClass) {
                $event->sender->attachBehavior($behaviorClass, $behaviorClass);
            });
        }
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
