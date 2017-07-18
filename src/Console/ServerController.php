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

class ServerController extends Controller
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
        return 'An event-driven, non-blocking web server.';
    }

    public function actionIndex()
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

                    if ($postBody) {
                        $body = json_decode($postBody, true);
                        $query = @$body['query'];
                        $variables = @$body['variables'] ?: [];
                    }

                    try {
                        if ($this->debug) { echo ' - Running: '.preg_replace('/[\r\n]+/', ' ', $query)."\n"; }
                        $result = $graphQl->execute($query, $variables);
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
}
