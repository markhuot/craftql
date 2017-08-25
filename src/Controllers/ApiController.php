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

        Craft::trace('CraftQL: Bootstrapping');
        $this->graphQl->bootstrap();
        Craft::trace('CraftQL: Bootstrapping complete');

        try {
            Craft::trace('CraftQL: Fetching schema');
            $schema = $this->graphQl->getSchema($token);
            Craft::trace('CraftQL: Schema built');
            Craft::trace('CraftQL: Executing query');
            $result = $this->graphQl->execute($schema, $this->request->input(), $this->request->variables());
            Craft::trace('CraftQL: Execution complete');
        } catch (\Exception $e) {
            // $backtrace = [];

            ob_start();
            debug_print_backtrace();
            $backtrace = ob_get_contents(); ob_end_clean();

            // foreach ($e->getTrace() as $index => $trace) {
            //     if ($index > 10) { break; }

            //     $backtrace[] = [
            //         'function' => $trace['function'],
            //         'file' => @$trace['file'],
            //         'line' => @$trace['line'],
            //     ];
            // }

            $result = [
                'errors' => [
                    'message' => $e->getMessage(),
                    'line' => $e->getLine(),
                    'file' => $e->getFile(),
                    'backtrace' => $backtrace,
                ]
            ];
        }

        if ($this->request->isDebugging() || false) {
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
