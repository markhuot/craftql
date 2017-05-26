<?php

namespace Craft;

use React\EventLoop\Factory;
use React\Socket\Server;
use React\Http\Response;
use React\Promise\Promise;
use Psr\Http\Message\ServerRequestInterface;

require_once rtrim(__DIR__, '/').'/../vendor/autoload.php';

class CraftQLServerCommand extends BaseCommand
{
    public function actionIndex()
    {
        craft()->craftQL_graphQL->bootstrap();

        $loop = Factory::create();
        $socket = new Server(isset($argv[1]) ? $argv[1] : '0.0.0.0:9001', $loop);

        $server = new \React\Http\Server($socket, function (ServerRequestInterface $request) {
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
                        $result = craft()->craftQL_graphQL->execute($query, $variables);
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
                    foreach (craft()->craftQL_graphQL->getTimers() as $key => $timer) {
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
        echo 'Listening on http://' . $socket->getAddress() . PHP_EOL;
        $loop->run();
    }
}
