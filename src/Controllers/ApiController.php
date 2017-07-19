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

    function actionGraphiql() {
        $url = Craft::$app->request->getUrl();
        $url = preg_replace('/\?.*$/', '', $url);

        $html = file_get_contents(dirname(__FILE__) . '/../templates/graphiql.html');
        $html = str_replace('{{ url }}', $url, $html);
        return $html;
    }

    function actionIndex()
    {
        $writable = true;
        $token = false;
        $user = Craft::$app->getUser()->getIdentity();

        if (!$user) {
            $authorization = Craft::$app->request->headers->get('Authorization');
            preg_match('/^bearer\s+(?<tokenId>.+)/', $authorization, $matches);
            $tokenId = @$matches['tokenId'];
            if ($tokenId) {
                $token = Token::find()->where(['token' => $tokenId])->one();
                if ($token) {
                    $writable = $token->isWritable;
                    $user = User::find()->where(['id' => $token->userId])->one();
                }
            }
        }

        // @todo, check user permissions when PRO license

        if (!$user) {
            http_response_code(403);
            header('Content-Type: application/json; charset=UTF-8');
            return json_encode([
                'errors' => [
                    ['message' => 'Not authorized']
                ]
            ]);
        }

        $this->graphQl->bootstrap($writable);

        try {
            $result = $this->graphQl->execute($this->request->input(), $this->request->variables());
        } catch (\Exception $e) {
            $result = [
                'errors' => [
                    'message' => $e->getMessage()
                ]
            ];
        }

        // You must set the header to JSON, otherwise Craft will see HTML and try to insert
        // javascript at the bottom to run pending tasks
        $headers = \Craft::$app->response->headers;
        $headers->add('Content-Type', 'application/json; charset=UTF-8');

        // $index = 1;
        // foreach ($this->graphQl->getTimers() as $key => $timer) {
        //     header('X-Timer-'.$index++.'-'.ucfirst($key).': '.$timer);
        // }

        return json_encode($result);
    }
}
