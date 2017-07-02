<?php

namespace markhuot\CraftQL\console;

use Craft;
use React\EventLoop\Factory;
use React\Socket\Server;
use React\Http\Response;
use React\Http\Server as HttpServer;
use React\Promise\Promise;
use Psr\Http\Message\ServerRequestInterface;
use yii\console\Controller;
use markhuot\CraftQL\Plugin;

class ServerController extends Controller
{
    public function actionIndex()
    {
        Plugin::$graphQLService->bootstrap();

        $loop = Factory::create();
        $socket = new Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:9001', $loop);

        $server = new HttpServer(function (ServerRequestInterface $request) {
            return new Promise(function ($resolve, $reject) use ($request) {
                $postBody = '';

                $request->getBody()->on('data', function ($data) use (&$postBody) {
                    $postBody .= $data;
                });

                $request->getBody()->on('end', function () use ($request, $resolve, &$postBody){
                    $query = false;
                    $variables = [];

                    parse_str($request->getUri()->getQuery(), $queryParams);
                    if (!empty($queryParams['query'])) {
                        $query = $queryParams['query'];
                    }

                    if ($postBody) {
                        $body = json_decode($postBody, true);
                        $query = $body['query'];
                        $variables = @$body['variables'] ?: [];
                    }

                    try {
                        echo ' - Running: '.preg_replace('/[\r\n]+/', ' ', $query)."\n";
                        $result = Plugin::$graphQLService->execute($query, $variables);
                    } catch (\Exception $e) {
                        $result = [
                            'error' => [
                                'message' => $e->getMessage()
                            ]
                        ];
                    }

                    $headers = [
                        'Content-Type' => 'application/json; charset=UTF-8',
                        'Access-Control-Allow-Origin' => '*',
                    ];

                    $index = 1;
                    foreach (Plugin::$graphQLService->getTimers() as $key => $timer) {
                        $headers['X-Timer-'.$index++.'-'.ucfirst($key)] = $timer;
                    }

                    $response = new Response(
                        200,
                        $headers,
                        json_encode($result)
                    );

                    $resolve($response);
                });
            });
        });
        echo 'Listening on ' . $socket->getAddress() . PHP_EOL;
        $loop->run();
    }
}
