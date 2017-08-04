<?php

namespace markhuot\CraftQL\Controllers;

use Craft;
use craft\web\Controller;
use craft\records\User;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\Models\Token;
use yii\web\ForbiddenHttpException;

class ApiController extends Controller
{
    protected $allowAnonymous = ['index'];

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

    function actionIndex()
    {
        // You must set the header to JSON, otherwise Craft will see HTML and try to insert
        // javascript at the bottom to run pending tasks
        $response = \Craft::$app->getResponse();
        $response->headers->add('Content-Type', 'application/json; charset=UTF-8');

        $token = false;
        
        $authorization = Craft::$app->request->headers->get('authorization');
        preg_match('/^(?:b|B)earer\s+(?<tokenId>.+)/', $authorization, $matches);
        $tokenId = @$matches['tokenId'];
        if ($tokenId) {
            $token = Token::find()->where(['token' => $tokenId])->one();
        }
        else {
            $user = Craft::$app->getUser()->getIdentity();
            if ($user) {
                $token = Token::forUser($user);
            }
        }

        // @todo, check user permissions when PRO license

        if (!$token) {
            http_response_code(403);
            $this->asJson([
                'errors' => [
                    ['message' => 'Not authorized']
                ]
            ]);
        }

        $this->graphQl->bootstrap($token);

        try {
            $result = $this->graphQl->execute($this->request->input(), $this->request->variables());
        } catch (\Exception $e) {
            $result = [
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        // $index = 1;
        // foreach ($this->graphQl->getTimers() as $key => $timer) {
        //     header('X-Timer-'.$index++.'-'.ucfirst($key).': '.$timer);
        // }

        $this->asJson($result);
    }
}
