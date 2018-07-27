<?php

namespace markhuot\CraftQL\Services;

use Craft;
use craft\elements\User;
use Firebase\JWT\JWT;
use markhuot\CraftQL\CraftQL;
use yii\base\Component;

class JWTService extends Component {

    private $key;

    function __construct($config=[]) {
        parent::__construct($config);

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

    function tokenForUser(User $user) {
        $defaultTokenDuration = CraftQL::getInstance()->getSettings()->userTokenDuration;

        $tokenData = [
            'id' => $user->id,
        ];

        if ($defaultTokenDuration > 0) {
            $tokenData['exp'] = time() + $defaultTokenDuration;
        }

        return $this->encode($tokenData);
    }

}
