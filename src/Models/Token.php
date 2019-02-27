<?php

namespace markhuot\CraftQL\Models;

use Craft;
use craft\db\ActiveRecord;
use craft\records\User;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\EnumType;
use GraphQL\Type\Definition\Type;
use markhuot\CraftQL\Builders\EnumObject;
use markhuot\CraftQL\CraftQL;
use markhuot\CraftQL\Repositories\Site;
use markhuot\CraftQL\Repositories\TagGroup;

class Token extends ActiveRecord
{
    private $admin = false;

    public function getUser() {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public static function findId($tokenId=false)
    {
        if ($tokenId) {
            return Token::find()->where(['token' => $tokenId])->one();
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

    /**
     * Check if this token has access to the requested scope. This method checks
     * three things to validate scopes,
     *
     *   1. are there any elements of the requested type. E.g., if you're asking if
     *      the token can query:globals it'll make sure there _are_ globals
     *   2. is this an admin token, if it is then all permissions are allowd
     *   3. is the scope enabled. If the scope is _not_ enabled it will not be in the
     *      the scopes array so our only check needs to be if the key is present.
     *      However, to build for the future we also allow false-y values in the scope
     *      array so we also check for truthyness on the scope
     *
     * @param string $do
     * @return bool
     */
    function can($do): bool {
        if (substr($do, 0, 6) === 'query:') {
            $service = substr($do, 6);
            if (in_array($service, ['globals'])) {
                if (count(CraftQL::$plugin->$service->all()) == 0) {
                    return false;
                }
            }
        }

        if ($this->admin) {
            return true;
        }

        return @$this->scopeArray[$do] ?: false;
    }

    function canNot($do): bool {
        return !$this->can($do);
    }

    function allowsMatch($regex): bool {
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