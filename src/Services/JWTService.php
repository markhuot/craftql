<?php

namespace markhuot\CraftQL\Services;

use Craft;
use Firebase\JWT\JWT;
use GraphQL\GraphQL;
use GraphQL\Error\Debug;
use GraphQL\Type\Schema;
use yii\base\Component;
use Yii;

class JWTService extends Component {

    private $key;

    function __construct() {
        if (!empty(\Craft::$app->config->craftql->securityKey)) {
            $this->key = \Craft::$app->config->craftql->securityKey;
        }
        else {
            $this->key = \Craft::$app->config->general->securityKey;
        }
    }

    function encode($string) {
        return JWT::encode($string, $this->key);
    }

    function decode($string) {
        return JWT::decode($string, $this->key, ['HS256']);
    }

}
