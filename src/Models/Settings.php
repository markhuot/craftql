<?php

namespace markhuot\CraftQL\Models;

use craft\base\Model;
use markhuot\CraftQL\Models\Token;

class Settings extends Model
{
    public $authorizationHeader = '';
    public $uri = 'api';
    public $verbs = ['POST'];
    public $allowedOrigins = [];
    public $headers = [];
    public $maxQueryDepth = false;
    public $maxQueryComplexity = false;

    function rules()
    {
        return [
            [['uri'], 'required'],
            // ...
        ];
    }

    function tokens()
    {
        $tokens = Token::find()->where(['userId' => \Craft::$app->user->id])->all();
        return $tokens;
    }
}