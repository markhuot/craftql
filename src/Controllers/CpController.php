<?php

namespace markhuot\CraftQL\Controllers;

use Craft;
use Yii;
use craft\web\Controller;
use markhuot\CraftQL\Plugin;
use markhuot\CraftQL\Models\Token;

class CpController extends Controller
{
    function actionTokengenerate()
    {
        $token = new Token;
        $token->userId = Craft::$app->getUser()->getIdentity()->id;
        $token->token = Yii::$app->security->generateRandomString(64);
        $token->isWritable = false;
        $token->save();

        $this->redirect('/admin/settings/plugins/craftql');
    }

    function actionTokendelete($tokenId)
    {
        $token = Token::find()->where(['id' => $tokenId])->one();
        $token->delete();

        $this->redirect('/admin/settings/plugins/craftql');
    }

    function actionTokenscopes($tokenId)
    {
        $this->renderTemplate('craftql/scopes', [
            'token' => Token::find()->where(['id' => $tokenId])->one()
        ]);
    }

    function actionSavetokenscopes($tokenId)
    {
        $token = Token::find()->where(['id' => $tokenId])->one();
        $token->name = $_POST['token']['name'];
        $token->scopes = json_encode(@$_POST['scope'] ?: []);
        $token->save();

        Craft::$app->getSession()->setNotice(Craft::t('app', 'Scopes saved.'));

        $this->redirect('/admin/craftql/token/'.$tokenId.'/scopes');
    }

    function actionIndex()
    {
        $this->redirect('craftql/browse');
    }

    function actionGraphiql()
    {
        $url = \craft\helpers\UrlHelper::siteUrl();
        $instance = \markhuot\CraftQL\Plugin::getInstance();
        $uri = $instance->settings->uri;

        $this->renderTemplate('craftql/graphiql', [
            'url' => "{$url}{$uri}",
            'token' => false,
        ]);
    }

    function actionGraphiqlas($token)
    {
        $url = \craft\helpers\UrlHelper::siteUrl();
        $instance = \markhuot\CraftQL\Plugin::getInstance();
        $uri = $instance->settings->uri;

        $this->renderTemplate('craftql/graphiql', [
            'url' => "{$url}{$uri}",
            'token' => $token,
        ]);
    }
}
