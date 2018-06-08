<?php

namespace markhuot\CraftQL\Models;

use Craft;
use craft\db\ActiveRecord;
use craft\records\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;

class Token extends ActiveRecord
{
    private $admin = false;

    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public static function findId($tokenId=false)
    {
        var_dump(preg_match('/[^.]+\.[^.]+\.[^.]+/', $tokenId));
        die;
        if ($tokenId) {
            return Token::find()->where(['token' => $tokenId])->one();
        }
        else if (preg_match('/[^.]+\.[^.]+\.[^.]+/', $tokenId)) {
            var_dump($tokenId);
        }
        else {
            $user = Craft::$app->getUser()->getIdentity();
            if ($user) {
                return Token::forUser($user);
            }
        }

        return false;
    }

    public static function admin(): Token
    {
        $token = new static;
        $token->scopes = json_encode([]);
        $token->makeAdmin();
        return $token;
    }

    public static function anonymous(): Token {
        $token = new static;
        $token->scopes = json_encode([]);
        return $token;
    }

    public static function forUser(): Token
    {
        $token = new static;
        $token->scopes = json_encode([]);
        $token->makeAdmin();
        return $token;
    }

    public function makeAdmin() {
        $this->admin = true;
    }

    /**
     * @return string The associated database table name
     */
    public static function tableName(): string
    {
        return '{{%craftql_tokens}}';
    }

    function getScopeArray(): array
    {
        return json_decode($this->scopes ?: '[]', true);
    }

    function can($do): bool {
        return $this->admin || @$this->scopeArray[$do] ?: false;
    }

    function canNot($do): bool {
        return !$this->can($do);
    }

    function canMatch($regex): bool {
        if ($this->admin) {
            return true;
        }

        $scopes = [];

        foreach ($this->scopeArray as $key => $value) {
            if ($value > 0 && preg_match($regex, $key)) {
                $scopes[] = $key;
            }
        }

        return count($scopes) > 0;
    }
}