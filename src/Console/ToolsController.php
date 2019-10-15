<?php

namespace markhuot\CraftQL\Console;

use Craft;
use GraphQL\Utils\SchemaPrinter;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Promise\Promise;
use Psr\Http\Message\ServerRequestInterface;
use yii\console\Controller;
use yii;
use markhuot\CraftQL\Services\GraphQLService;
use markhuot\CraftQL\Models\Token;
use markhuot\CraftQL\CraftQL;

class ToolsController extends Controller
{
    public $port = 9001;
    public $host = '0.0.0.0';
    public $debug = false;

    public function options($actionID)
    {
        return ['port', 'host', 'debug'];
    }

    public function optionAliases()
    {
        return [
            'p' => 'port',
            'h' => 'host'
        ];
    }

    public function getHelpSummary() {
        return 'Tools for CraftQL';
    }

    public function getActionHelpSummary($action) {
        switch ($action->actionMethod) {
            case 'actionServe': return 'An event-driven, non-blocking web server.';
            case 'actionSeed': return 'Create sample sections to test out CraftQL.';
            case 'actionDownloadFragmentTypes': return 'Downloads a JSON file of fragment types to be passed to Apollo Client';
            case 'actionPrintSchema': return 'Prints a .graphql type file to std out';
        }
    }

    public function actionServe()
    {
        $graphQl = Yii::$container->get(GraphQLService::class);
        $graphQl->bootstrap();

        $loop = \React\EventLoop\Factory::create();

        $server = new HttpServer(function (ServerRequestInterface $request) use ($graphQl) {
            return new Promise(function ($resolve, $reject) use ($request, $graphQl) {
                $postBody = '';

                $request->getBody()->on('data', function ($data) use (&$postBody) {
                    $postBody .= $data;
                });

                $request->getBody()->on('end', function () use ($request, $resolve, &$postBody, $graphQl) {
                    $query = false;
                    $variables = [];

                    $authorization = @$request->getHeaders()['authorization'][0];
                    preg_match('/^(?:b|B)earer\s+(?<tokenId>.+)/', $authorization, $matches);
                    $token = Token::findId(@$matches['tokenId']);

                    // @todo, check user permissions when PRO license

                    $headers = [
                        'Content-Type' => 'application/json; charset=UTF-8',
                    ];
                    if ($allowedOrigins = CraftQL::getInstance()->getSettings()->allowedOrigins) {
                        if (is_string($allowedOrigins)) {
                            $allowedOrigins = [$allowedOrigins];
                        }
                        $origin = $request->getHeaderLine('Origin');
                        if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
                            $headers['Access-Control-Allow-Origin'] = $origin;
                        }
                        $headers['Access-Control-Allow-Credentials'] = 'true';
                        $headers['Access-Control-Allow-Headers'] = implode(', ', CraftQL::getInstance()->getSettings()->allowedHeaders);

                    }
                    $headers['Allow'] = implode(', ', CraftQL::getInstance()->getSettings()->verbs);

                    if ($request->getMethod() == 'OPTIONS') {
                        return $resolve(new Response(200, $headers, ''));
                    }

                    if (!$token) {
                        $response = new Response(403, $headers,
                            json_encode([
                                'errors' => [
                                    ['message' => 'Not authorized']
                                ]
                            ])
                        );
                        return $resolve($response);
                    }

                    if ($postBody) {
                        $body = json_decode($postBody, true);
                        $query = @$body['query'];
                        $variables = @$body['variables'] ?: [];
                    }

                    if ($this->debug) { echo ' - Running: '.preg_replace('/[\r\n]+/', ' ', $query)."\n"; }
                    $schema = $graphQl->getSchema($token);
                    try {
                        $result = $graphQl->execute($schema, $query, $variables);
                    } catch (\Exception $e) {
                        if ($this->debug) { echo $e; }
                    }

                    $resolve(new Response(200, $headers, json_encode($result)));
                });
            });
        });

        $socket = new \React\Socket\Server($this->host.':'.$this->port, $loop);
        $server->listen($socket);

        echo "Server is now listening at http://{$this->host}:{$this->port}\n";
        $loop->run();
    }

    public function actionSeed()
    {
        $homeSection = new \craft\models\Section;
        $homeSection->name = 'Homepage';
        $homeSection->handle = 'homepage';
        $homeSection->type = 'single';
        $homeSection->enableVersioning = true;

        $homeSectionSiteSettings = new \craft\models\Section_SiteSettings();
        $homeSectionSiteSettings->siteId = 1;
        $homeSectionSiteSettings->hasUrls = true;
        $homeSectionSiteSettings->uriFormat = '/';
        $homeSectionSiteSettings->template = 'homepage/_entry';
        $homeSectionSiteSettings->enabledByDefault = true;
        $homeSection->setSiteSettings([1 => $homeSectionSiteSettings]);

        Craft::$app->getSections()->saveSection($homeSection);

        // ----------

        $destinationSection = new \craft\models\Section;
        $destinationSection->name = 'Destinations';
        $destinationSection->handle = 'destinations';
        $destinationSection->type = 'channel';
        $destinationSection->enableVersioning = true;

        $destinationSectionSiteSettings = new \craft\models\Section_SiteSettings();
        $destinationSectionSiteSettings->siteId = 1;
        $destinationSectionSiteSettings->hasUrls = true;
        $destinationSectionSiteSettings->uriFormat = 'destinations/{slug}';
        $destinationSectionSiteSettings->template = 'destination/_entry';
        $destinationSectionSiteSettings->enabledByDefault = true;
        $destinationSection->setSiteSettings([1 => $destinationSectionSiteSettings]);

        Craft::$app->getSections()->saveSection($destinationSection);

        // ----------

        $section = new \craft\models\Section;
        $section->name = 'Stories';
        $section->handle = 'stories';
        $section->type = 'channel';
        $section->enableVersioning = false;

        $siteSettings = new \craft\models\Section_SiteSettings();
        $siteSettings->siteId = 1;
        $siteSettings->hasUrls = true;
        $siteSettings->uriFormat = 'stories/{slug}';
        $siteSettings->template = 'stories/_story';
        $siteSettings->enabledByDefault = true;
        $section->setSiteSettings([1 => $siteSettings]);

        Craft::$app->getSections()->saveSection($section);

        // ----------

        if (!file_exists('./web/uploads')) {
            mkdir('./web/uploads');
        }

        $volume = Craft::$app->volumes->createVolume([
            'type' => 'craft\volumes\Local',
            'name' => 'Default Volume',
            'handle' => 'defaultVolume',
            'hasUrls' => true,
            'url' => '/uploads',
            'settings' => json_encode(['path' => realpath('./web/uploads')]),
        ]);

        Craft::$app->volumes->saveVolume($volume);

        // ----------

        $groupModel = new \craft\models\FieldGroup();
        $groupModel->name = 'Default';

        Craft::$app->fields->saveGroup($groupModel);

        $groups = Craft::$app->fields->getAllGroups();
        foreach ($groups as $group) {
            if ($group->name == 'Default') {
                $groupModel = $group;
            }
        }

        $bodyField = new \craft\fields\PlainText();
        $bodyField->groupId = $groupModel->id;
        $bodyField->name = 'Body';
        $bodyField->handle = 'body';
        $bodyField->required = false;
        $bodyField->sortOrder = 0;
        Craft::$app->fields->saveField($bodyField);

        $headingField = new \craft\fields\PlainText();
        $headingField->groupId = $groupModel->id;
        $headingField->name = 'Heading';
        $headingField->handle = 'heading';
        $headingField->required = false;
        $headingField->sortOrder = 0;
        Craft::$app->fields->saveField($headingField);

        $subheadingField = new \craft\fields\PlainText();
        $subheadingField->groupId = $groupModel->id;
        $subheadingField->name = 'Subheading';
        $subheadingField->handle = 'subheading';
        $subheadingField->required = false;
        $subheadingField->sortOrder = 0;
        Craft::$app->fields->saveField($subheadingField);

        $dateField = new \craft\fields\Date();
        $dateField->groupId = $groupModel->id;
        $dateField->name = 'Release Date';
        $dateField->handle = 'releaseDate';
        $dateField->required = false;
        $dateField->sortOrder = 0;
        Craft::$app->fields->saveField($dateField);

        $lightswitchField = new \craft\fields\Lightswitch();
        $lightswitchField->groupId = $groupModel->id;
        $lightswitchField->name = 'Promoted';
        $lightswitchField->handle = 'promoted';
        $lightswitchField->required = false;
        $lightswitchField->sortOrder = 0;
        Craft::$app->fields->saveField($lightswitchField);

        $checkboxesField = new \craft\fields\Checkboxes();
        $checkboxesField->groupId = $groupModel->id;
        $checkboxesField->name = 'Social Links';
        $checkboxesField->handle = 'socialLinks';
        $checkboxesField->required = false;
        $checkboxesField->sortOrder = 0;
        $checkboxesField->options = [
            ['label' => 'Facebook', 'value' => 'fb', 'default' => false],
            ['label' => 'Twitter', 'value' => 'tw', 'default' => false],
            ['label' => 'LinkedIn', 'value' => 'ln', 'default' => false],
            ['label' => 'Instagram', 'value' => 'in', 'default' => false],
        ];
        Craft::$app->fields->saveField($checkboxesField);

        $dropdownField = new \craft\fields\Dropdown();
        $dropdownField->groupId = $groupModel->id;
        $dropdownField->name = 'Language';
        $dropdownField->handle = 'language';
        $dropdownField->required = false;
        $dropdownField->sortOrder = 0;
        $dropdownField->options = [
            ['label' => 'English', 'value' => 'en', 'default' => false],
            ['label' => 'French', 'value' => 'fr', 'default' => false],
            ['label' => 'German', 'value' => 'de', 'default' => true],
            ['label' => 'Chinese', 'value' => 'cn', 'default' => false],
        ];
        Craft::$app->fields->saveField($dropdownField);

        $entriesField = new \craft\fields\Entries();
        $entriesField->groupId = $groupModel->id;
        $entriesField->name = 'Related Entry';
        $entriesField->handle = 'relatedEntry';
        $entriesField->required = false;
        $entriesField->sortOrder = 0;
        Craft::$app->fields->saveField($entriesField);

        $emptyMatrixField = new \craft\fields\Matrix();
        $emptyMatrixField->groupId = $groupModel->id;
        $emptyMatrixField->name = 'Empty Matrix Field';
        $emptyMatrixField->handle = 'emptyMatrixField';
        $emptyMatrixField->required = false;
        $emptyMatrixField->sortOrder = 0;
        Craft::$app->fields->saveField($emptyMatrixField);

        $emptyMatrixBlockField = new \craft\fields\Matrix();
        $emptyMatrixBlockField->groupId = $groupModel->id;
        $emptyMatrixBlockField->name = 'Empty Matrix Fields';
        $emptyMatrixBlockField->handle = 'emptyMatrixBlockFields';
        $emptyMatrixBlockField->required = false;
        $emptyMatrixBlockField->sortOrder = 0;
        $emptyMatrixBlockField->setBlockTypes([
            'new1' => [
                'name' => 'Empty Block',
                'handle' => 'emptyBlock',
                'fields' => []
            ],
        ]);
        Craft::$app->fields->saveField($emptyMatrixBlockField);

        $matrixField = new \craft\fields\Matrix();
        $matrixField->groupId = $groupModel->id;
        $matrixField->name = 'Rich Content';
        $matrixField->handle = 'richContent';
        $matrixField->required = false;
        $matrixField->sortOrder = 0;
        $matrixField->setBlockTypes([
            'new1' => [
                'name' => 'Text',
                'handle' => 'text',
                'fields' => [
                    'new1' => [
                        'type' => \craft\fields\PlainText::class,
                        'name' => 'Content',
                        'handle' => 'textContent',
                        'instructions' => null,
                        'required' => false,
                    ]
                ]
            ],
            'new2' => [
                'name' => 'Related Cotnent',
                'handle' => 'relatedContent',
                'fields' => [
                    'new1' => [
                        'type' => \craft\fields\PlainText::class,
                        'name' => 'Heading',
                        'handle' => 'heading',
                        'instructions' => null,
                        'required' => false,
                    ],
                    'new2' => [
                        'type' => \craft\fields\Entries::class,
                        'name' => 'Related Entry',
                        'handle' => 'matrixRelatedEntry',
                        'instructions' => null,
                        'required' => false,
                    ],
                ]
            ],
        ]);
        Craft::$app->fields->saveField($matrixField);

        $multiSelectField = new \craft\fields\MultiSelect();
        $multiSelectField->groupId = $groupModel->id;
        $multiSelectField->name = 'Social Links';
        $multiSelectField->handle = 'socialLinksTwo';
        $multiSelectField->required = false;
        $multiSelectField->sortOrder = 0;
        $multiSelectField->options = [
            ['label' => 'Facebook', 'value' => 'fb', 'default' => false],
            ['label' => 'Twitter', 'value' => 'tw', 'default' => false],
            ['label' => 'LinkedIn', 'value' => 'ln', 'default' => false],
            ['label' => 'Instagram', 'value' => 'in', 'default' => false],
        ];
        Craft::$app->fields->saveField($multiSelectField);

        $heroImageField = new \craft\fields\Assets();
        $heroImageField->groupId = $groupModel->id;
        $heroImageField->name = 'Hero Image';
        $heroImageField->handle = 'heroImage';
        $heroImageField->required = false;
        $heroImageField->sortOrder = 0;
        $heroImageField->useSingleFolder = false;
        $heroImageField->defaultUploadLocationSource = "folder:1";
        $heroImageField->defaultUploadLocationSubpath = "";
        $heroImageField->singleUploadLocationSource = "folder:1";
        $heroImageField->singleUploadLocationSubpath = "";
        $heroImageField->restrictFiles = "";
        $heroImageField->allowedKinds = null;
        Craft::$app->fields->saveField($heroImageField);

        $categoryGroup = new \craft\models\CategoryGroup();
        $categoryGroup->name = 'Story Types';
        $categoryGroup->handle = 'storyTypes';
        $categoryGroup->maxLevels = null;
        $groupSiteSettings = new \craft\models\CategoryGroup_SiteSettings();
        $groupSiteSettings->siteId = 1;
        $groupSiteSettings->hasUrls = true;
        $groupSiteSettings->uriFormat = 'type/{slug}';
        $groupSiteSettings->template = 'type/_story';
        $categoryGroup->setSiteSettings([1 => $groupSiteSettings]);
        Craft::$app->getCategories()->saveGroup($categoryGroup);

        $categoryField = new \craft\fields\Categories();
        $categoryField->groupId = $groupModel->id;
        $categoryField->name = 'Story Types';
        $categoryField->handle = 'storyTypes';
        $categoryField->required = false;
        $categoryField->sortOrder = 0;
        $categoryField->source = 'group:'.$categoryGroup->id;
        Craft::$app->fields->saveField($categoryField);

        $tagGroup = new \craft\models\TagGroup();
        $tagGroup->name = 'Story Tags';
        $tagGroup->handle = 'storyTags';
        Craft::$app->getTags()->saveTagGroup($tagGroup);

        $tagField = new \craft\fields\Tags();
        $tagField->groupId = $groupModel->id;
        $tagField->name = 'Story Tags';
        $tagField->handle = 'storyTags';
        $tagField->required = false;
        $tagField->sortOrder = 0;
        $tagField->source = 'taggroup:'.$tagGroup->id;
        Craft::$app->fields->saveField($tagField);

        // ----------

        $homepageLayout = new \craft\models\FieldLayout();
        $homepageLayout->type = \craft\elements\Entry::class;

        $contentTab = new \craft\models\FieldLayoutTab();
        $contentTab->setLayout($homepageLayout);
        $contentTab->name = 'Content';
        $contentTab->setFields([
            $heroImageField,
            $headingField,
            $subheadingField,
        ]);

        if (!empty($homeSection->getEntryTypes())) {
            $entryType = $homeSection->getEntryTypes()[0];
            $homepageLayout = Craft::$app->fields->getLayoutById($entryType->fieldLayoutId);
            $homepageLayout->setTabs([
                $contentTab,
            ]);
            Craft::$app->fields->saveLayout($homepageLayout);
        }

        // ----------

        $destinationLayout = new \craft\models\FieldLayout();
        $destinationLayout->type = \craft\elements\Entry::class;

        $destinationTab = new \craft\models\FieldLayoutTab();
        $destinationTab->setLayout($destinationLayout);
        $destinationTab->name = 'Content';
        $destinationTab->setFields([
            $matrixField,
        ]);

        if (!empty($destinationSection->getEntryTypes())) {
            $entryType = $destinationSection->getEntryTypes()[0];
            $destinationLayout = Craft::$app->fields->getLayoutById($entryType->fieldLayoutId);
            $destinationLayout->setTabs([
                $destinationTab,
            ]);
            Craft::$app->fields->saveLayout($destinationLayout);
        }

        // ----------

        $storiesLayout = new \craft\models\FieldLayout();
        $storiesLayout->type = \craft\elements\Entry::class;

        $contentTab = new \craft\models\FieldLayoutTab();
        $contentTab->setLayout($storiesLayout);
        $contentTab->name = 'Content';
        $contentTab->setFields([
            $bodyField,
            $dateField,
            $lightswitchField,
            $checkboxesField,
            $dropdownField,
            $entriesField,
            $multiSelectField,
            $heroImageField,
            $matrixField,
            $emptyMatrixField,
            $emptyMatrixBlockField,
            $categoryField,
            $tagField,
        ]);

        if (!empty($section->getEntryTypes())) {
            $entryType = $section->getEntryTypes()[0];
            $storiesLayout = Craft::$app->fields->getLayoutById($entryType->fieldLayoutId);
            $storiesLayout->setTabs([
                $contentTab,
            ]);
            Craft::$app->fields->saveLayout($storiesLayout);
        }

        // ----------

        $globalMetaDescriptionField = new \craft\fields\PlainText();
        $globalMetaDescriptionField->groupId = $groupModel->id;
        $globalMetaDescriptionField->name = 'Meta Description';
        $globalMetaDescriptionField->handle = 'metaDescription';
        $globalMetaDescriptionField->required = false;
        $globalMetaDescriptionField->sortOrder = 0;
        Craft::$app->fields->saveField($globalMetaDescriptionField);

        $globalFieldLayout = new \craft\models\FieldLayout();
        $globalFieldLayout->type = \craft\elements\GlobalSet::class;

        $globalLayoutTab = new \craft\models\FieldLayoutTab();
        $globalLayoutTab->setLayout($globalFieldLayout);
        $globalLayoutTab->name = 'Content';
        $globalLayoutTab->setFields([
            $globalMetaDescriptionField,
        ]);
        $globalFieldLayout->setTabs([$globalLayoutTab]);
        Craft::$app->fields->saveLayout($globalFieldLayout);

        $globalSet = new \craft\elements\GlobalSet();
        $globalSet->name = "SEO";
        $globalSet->handle = 'seo';
        $globalSet->setFieldLayout($globalFieldLayout);
        Craft::$app->getGlobals()->saveSet($globalSet);
    }

    public function actionFetchFragmentTypes() {
        $graphQl = Yii::$container->get(GraphQLService::class);
        $graphQl->bootstrap();

        $token = Token::admin();

        $query = '{
            __schema {
                types {
                    kind
                    name
                    possibleTypes {
                        name
                    }
                }
            }
        }';
        $variables = [];

        $schema = $graphQl->getSchema($token);
        $result = $graphQl->execute($schema, $query, $variables);

        foreach ($result['data']['__schema']['types'] as $index => $type) {
            if (empty($type['possibleTypes'])) {
                unset($result['data']['__schema']['types'][$index]);
            }
        }

        // because we removed some types our index isn't incremental any more which
        // will cause PHP to json_encode types in to an object, not an array, we'll
        // merge down the array back to itself to reset the keys so
        // they're incremental
        $result['data']['__schema']['types'] = array_merge($result['data']['__schema']['types']);

        echo json_encode($result['data']);
    }

    public function actionPrintSchema() {
        $graphQl = Yii::$container->get(GraphQLService::class);
        $graphQl->bootstrap();
        $token = Token::admin();
        $schema = $graphQl->getSchema($token);
        echo SchemaPrinter::doPrint($schema);
    }
}
