<?php

namespace markhuot\CraftQL\Controllers;

use craft\web\Controller;
use markhuot\CraftQL\Plugin;

class ApiController extends Controller
{
    protected $allowAnonymous = ['index','graphiql'];

    private $graphQl;
    private $request;

    function __construct(
        $id,
        $module, 
        \markhuot\CraftQL\Services\GraphQLService $graphQl,
        \markhuot\CraftQL\Services\RequestService $request,
        $config = []
    ) {
        parent::__construct($id, $module, $config);

        $this->graphQl = $graphQl;
        $this->request = $request;
    }

    function actionGraphiql() {
        return file_get_contents(dirname(__FILE__) . '/../../graphiql/index.html');
    }

    function actionIndex()
    {
        $input = $this->request->input();
        $variables = $this->request->variables();

        $this->graphQl->bootstrap();

        try {
            $result = $this->graphQl->execute($input, $variables);
        } catch (\Exception $e) {
            $result = [
                'error' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Headers: Content-Type');

        $index = 1;
        foreach ($this->graphQl->getTimers() as $key => $timer) {
            header('X-Timer-'.$index++.'-'.ucfirst($key).': '.$timer);
        }

        echo json_encode($result);
    }
}
