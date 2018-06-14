<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Firebase\JWT\JWT;
use markhuot\CraftQL\CraftQL;
use yii\base\Component;

class JWTService extends Component {

    private $key;

    function __construct() {
        if (CraftQL::getInstance()->getSettings()->securityKey) {
            $this->key = CraftQL::getInstance()->getSettings()->securityKey;
        }
        else {
            $this->key = Craft::$app->config->general->securityKey;
        }
    }

    function encode($string) {
        return JWT::encode($string, $this->key);
    }

    function decode($string) {
        return JWT::decode($string, $this->key, ['HS256']);
    }

}
