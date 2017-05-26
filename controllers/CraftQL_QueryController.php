<?php

namespace Craft;

require_once rtrim(__DIR__, '/').'/../vendor/autoload.php';

class CraftQL_QueryController extends BaseController
{
    protected $allowAnonymous = true;

    function actionQuery()
    {
        $input = craft()->craftQL_request->input();

        craft()->craftQL_graphQL->bootstrap();

        try {
            $result = craft()->craftQL_graphQL->execute($input);
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
        foreach (craft()->craftQL_graphQL->getTimers() as $key => $timer) {
            header('X-Timer-'.$index++.'-'.ucfirst($key).': '.$timer);
        }

        echo json_encode($result);
    }
}
