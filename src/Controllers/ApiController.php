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

    function __construct(
        $id,
        $module,
        \markhuot\CraftQL\Services\GraphQLService $graphQl,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
        $this->graphQl = $graphQl;
    }

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
        $settings = CraftQL::getInstance()->getSettings();

        $token = static::findToken($settings->authorizationHeader, Craft::$app->request->headers);

        // @todo, check user permissions when PRO license

        $response = \Craft::$app->getResponse();
        if ($allowedOrigins = $settings->allowedOrigins) {
            if (is_string($allowedOrigins)) {
                $allowedOrigins = [$allowedOrigins];
            }
            $origin = \Craft::$app->getRequest()->headers->get('Origin');
            if (in_array($origin, $allowedOrigins) || in_array('*', $allowedOrigins)) {
                $response->headers->add('Access-Control-Allow-Origin', $origin);
            }
            $response->headers->add('Access-Control-Allow-Credentials', 'true');

            $allowedHeaders = ['Authorization', 'Content-Type'];
            if ($settings->authorizationHeader) {
                $allowedHeaders[] = $settings->authorizationHeader;
            }

            $response->headers->add('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }
        $response->headers->add('Allow', implode(', ', $settings->verbs));

        if (\Craft::$app->getRequest()->isOptions) {
            return '';
        }

        if (!$token) {
            http_response_code(403);
            return $this->asJson([
                'errors' => [
                    ['message' => 'Not authorized']
                ]
            ]);
        }

        Craft::trace('CraftQL: Parsing request');
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
        Craft::trace('CraftQL: Parsing request complete');

        Craft::trace('CraftQL: Bootstrapping');
        $this->graphQl->bootstrap();
        Craft::trace('CraftQL: Bootstrapping complete');

        Craft::trace('CraftQL: Fetching schema');
        $schema = $this->graphQl->getSchema($token);
        Craft::trace('CraftQL: Schema built');

        Craft::trace('CraftQL: Executing query');
        $result = $this->graphQl->execute($schema, $input, $variables);
        Craft::trace('CraftQL: Execution complete');

        $customHeaders = $settings->headers ?: [];
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

    private static function findToken($authorizationHeader, $headers)
    {
        // Default, for cases when token header is not found to use user based token
        // resolution, if possible.
        $tokenId = null;

        if ($authorizationHeader) {
            if ($headers->has($authorizationHeader)) {
                $tokenId = $headers->get($authorizationHeader);
            }
        } else if ($headers->has('authorization')) {
            $authorization = $headers->get('authorization');
            preg_match('/^(?:b|B)earer\s+(?<tokenId>.+)/', $authorization, $matches);
            $tokenId = @$matches['tokenId'];
        }

        return Token::findId($tokenId);
    }
}
