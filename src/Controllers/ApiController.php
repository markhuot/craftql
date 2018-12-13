<?php

namespace markhuot\CraftQL\Controllers;

use Craft;
use craft\web\Controller;
use craft\records\User;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Models\Token;
use yii\web\ForbiddenHttpException;

class ApiController extends Controller
{
    protected $allowAnonymous = ['index'];

    private $graphQl;
    private $request;

    /**
     * @inheritdoc
     */
    public function beforeAction($action)
    {
        // disable csrf
        $this->enableCsrfValidation = false;

        return parent::beforeAction($action);
    }

    function actionDebug() {
        $oldMode = \Craft::$app->getView()->getTemplateMode();
        \Craft::$app->getView()->setTemplateMode(\craft\web\View::TEMPLATE_MODE_CP);
        $data = $this->getView()->renderPageTemplate('craftql/debug-input', []);
        \Craft::$app->getView()->setTemplateMode($oldMode);
        return $data;
    }

    function actionIndex()
    {
        $response = \Craft::$app->getResponse();

        $authorization = Craft::$app->request->headers->get('authorization');
        preg_match('/^(?:b|B)earer\s+(?<tokenId>.+)/', $authorization, $matches);
        $token = Token::findOrAnonymous(@$matches['tokenId']);

        if ($user = $token->getUser()) {
            $response->headers->add('Authorization', 'Bearer ' . CraftQL::getInstance()->jwt->tokenForUser($user));
        }

        if ($allowedOrigins = CraftQL::getInstance()->getSettings()->allowedOrigins) {
            if (is_string($allowedOrigins)) {
                $allowedOrigins = [$allowedOrigins];
            }
            $origin = \Craft::$app->getRequest()->headers->get('Origin');
            if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
                $response->headers->add('Access-Control-Allow-Origin', $origin);
            }
            $response->headers->add('Access-Control-Allow-Credentials', 'true');
            $response->headers->add('Access-Control-Allow-Headers', 'Authorization, Content-Type');
            $response->headers->add('Access-Control-Expose-Headers', 'Authorization');
        }
        $response->headers->add('Allow', implode(', ', CraftQL::getInstance()->getSettings()->verbs));

        if (\Craft::$app->getRequest()->isOptions) {
            return '';
        }

        Craft::debug('CraftQL: Parsing request');
        if (Craft::$app->request->isPost && $query=Craft::$app->request->post('query')) {
            $input = $query;
        }
        else if (Craft::$app->request->isGet && $query=Craft::$app->request->get('query')) {
            $input = $query;
        }
        else {
            $data = Craft::$app->request->getRawBody();
            $data = json_decode($data, true);
            $input = @$data['query'];
        }

        if (Craft::$app->request->isPost && $query=Craft::$app->request->post('variables')) {
            $variables = $query;
        }
        else if (Craft::$app->request->isGet && $query=Craft::$app->request->get('variables')) {
            $variables = json_decode($query, true);
        }
        else {
            $data = Craft::$app->request->getRawBody();
            $data = json_decode($data, true);
            $variables = @$data['variables'];
        }
        Craft::debug('CraftQL: Parsing request complete');

        Craft::debug('CraftQL: Bootstrapping');
        CraftQL::getInstance()->graphQl->bootstrap();
        Craft::debug('CraftQL: Bootstrapping complete');

        Craft::debug('CraftQL: Fetching schema');
        $schema = CraftQL::getInstance()->graphQl->getSchema($token);
        Craft::debug('CraftQL: Schema built');

        Craft::debug('CraftQL: Executing query');
        $result = CraftQL::getInstance()->graphQl->execute($schema, $input, $variables);
        Craft::debug('CraftQL: Execution complete');

        $customHeaders = CraftQL::getInstance()->getSettings()->headers ?: [];
        foreach ($customHeaders as $key => $value) {
            if (is_callable($value)) {
                $value = $value($schema, $input, $variables, $result);
            }
            $response = \Craft::$app->getResponse();
            $response->headers->add($key, $value);
        }

        if (!!Craft::$app->request->post('debug')) {
            $response = \Yii::$app->getResponse();
            $response->format = \craft\web\Response::FORMAT_HTML;

            $oldMode = \Craft::$app->getView()->getTemplateMode();
            \Craft::$app->getView()->setTemplateMode(\craft\web\View::TEMPLATE_MODE_CP);
            $response->data = $this->getView()->renderPageTemplate('craftql/debug-response', ['json' => json_encode($result)]);
            \Craft::$app->getView()->setTemplateMode($oldMode);

            return $response;
        }

        // You must set the header to JSON, otherwise Craft will see HTML and try to insert
        // javascript at the bottom to run pending tasks
        $response = \Craft::$app->getResponse();
        $response->headers->add('Content-Type', 'application/json; charset=UTF-8');

        return $this->asJson($result);
    }
}
