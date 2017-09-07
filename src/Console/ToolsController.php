<?php

namespace markhuot\CraftQL\Console;

use Craft;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Promise\Promise;
use Psr\Http\Message\ServerRequestInterface;
use yii\console\Controller;
use yii;
use markhuot\CraftQL\Services\GraphQLService;
use markhuot\CraftQL\Models\Token;

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

                    $authorization = @$request->getHeaders()['Authorization'][0];
                    preg_match('/^(?:b|B)earer\s+(?<tokenId>.+)/', $authorization, $matches);
                    $token = Token::findId(@$matches['tokenId']);

                    // @todo, check user permissions when PRO license

                    if (!$token) {
                        $response = new Response(403, [
                                'Content-Type' => 'application/json; charset=UTF-8',
                                'Access-Control-Allow-Origin' => '*',
                            ],
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

                    try {
                        if ($this->debug) { echo ' - Running: '.preg_replace('/[\r\n]+/', ' ', $query)."\n"; }
                        $graphQl->bootstrap();
                        $schema = $graphQl->getSchema($token);
                        $result = $graphQl->execute($schema, $query, $variables);
                    } catch (\Exception $e) {
                        $result = [
                            'error' => [
                                'message' => $e->getMessage()
                            ]
                        ];
                    }

                    $response = new Response(200, [
                            'Content-Type' => 'application/json; charset=UTF-8',
                            'Access-Control-Allow-Origin' => '*',
                        ],
                        json_encode($result)
                    );
                    $resolve($response);
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
        $section = new \craft\models\Section;
        $section->name = 'Stories';
        $section->handle = 'stories';
        $section->type = 'channel';
        $section->enableVersioning = false;

        $siteSettings = new \craft\models\Section_SiteSettings();
        $siteSettings->hasUrls = true;
        $siteSettings->uriFormat = 'stories/{slug}';
        $siteSettings->template = 'stories/_story';
        $section->setSiteSettings([1 => $siteSettings]);
        
        Craft::$app->sections->saveSection($section);

        $groupModel = new \craft\models\FieldGroup();
        $groupModel->name = 'Default';

        Craft::$app->fields->saveGroup($groupModel);

        $groups = Craft::$app->fields->getAllGroups();
        foreach ($groups as $group) {
            if ($group->name == 'Default') {
                $groupModel = $group;
            }
        }

        $bodyField = new \craft\fields\RichText();
        $bodyField->groupId = $groupModel->id;
        $bodyField->name = 'Body';
        $bodyField->handle = 'body';
        $bodyField->required = false;
        $bodyField->sortOrder = 0;

        Craft::$app->fields->saveField($bodyField);

        $layout = new \craft\models\FieldLayout();
        $layout->type = \craft\elements\Entry::class;

        $contentTab = new \craft\models\FieldLayoutTab();
        $contentTab->setLayout($layout);
        $contentTab->name = 'Content';
        $contentTab->setFields([
            $bodyField,
        ]);

        if (!empty($section->getEntryTypes())) {
            $entryType = $section->getEntryTypes()[0];
            $layout = Craft::$app->fields->getLayoutById($entryType->fieldLayoutId);
            $layout->setTabs([
                $contentTab,
            ]);
            Craft::$app->fields->saveLayout($layout);
        }
    }
}
