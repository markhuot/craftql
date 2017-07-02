<?php

namespace markhuot\CraftQL\Controllers;

use craft\web\Controller;
use markhuot\CraftQL\Plugin;

class ApiController extends Controller
{
    protected $allowAnonymous = ['index'];

    function actionIndex()
    {
        $input = Plugin::$requestService->input();

        Plugin::$graphQLService->bootstrap();

        try {
            $result = Plugin::$graphQLService->execute($input);
        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Origin: http://localhost:4000');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type');

        $index = 1;
        foreach (Plugin::$graphQLService->getTimers() as $key => $timer) {
            header('X-Timer-'.$index++.'-'.ucfirst($key).': '.$timer);
        }

        echo json_encode($result);
    }
}
