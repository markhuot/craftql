<?php

namespace markhuot\CraftQL\Models;

use Craft;
use craft\db\ActiveRecord;
use markhuot\CraftQL\CraftQL;

class Token extends ActiveRecord
{
    /**
     * Whether the token represents an admin role
     *
     * @var bool
     */
    private $admin = false;

    /**
     * @var \craft\elements\User
     */
    private $user = false;

    /**
     * @return \craft\elements\User
     */
    function getUser() {
        return $this->user;
    }

    /**
     * Sets the user
     *
     * @param \craft\elements\User $user
     */
    function setUser (\craft\elements\User $user) {
        $this->user = $user;
    }

    /**
     * Gets a token by the token id
     *
     * @param bool $token
     * @return Token|null
     */
    public static function findOrAnonymous($token=false)
    {
        // If the token matches a JWT format
        if ($token && preg_match('/[^.]+\.[^.]+\.[^.]+/', $token)) {
            $tokenData = CraftQL::getInstance()->jwt->decode($token);
            $userRow = (new \craft\db\Query())
                ->from('users')
                ->where(['uid' => $tokenData->uid])
                ->limit(1)
                ->one();
            $user = \craft\elements\User::find()->id($userRow['id'])->one();
            $token = Token::forUser($user);
            $token->setUser($user);
            return $token;
        }

        // If the token is in the database
        else if ($token && $token=Token::find()->where(['token' => $token])->one()) {
            return $token;
        }

        // If the user has an active Craft session
        else if ($user = Craft::$app->getUser()->getIdentity()) {
            return Token::forUser($user);
        }

        return static::anonymous();
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

    public static function forUser(\craft\elements\User $user): Token
    {
        $token = new static;

        $scopes = [];
        $permissions = Craft::$app->getUserPermissions()->getPermissionsByUserId($user->id);
        foreach ($permissions as $permission) {
            if (substr($permission, 0, 8) == 'craftql:') {
                $scopes[substr($permission, 8)] = 1;
            }
        }
        $token->scopes = json_encode($scopes);

        if ($user->admin) {
            $token->makeAdmin();
        }

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
        $scopes = [];

        $rawScopes = json_decode($this->scopes ?: '[]', true);
        foreach ($rawScopes as $key => $value) {
            $scopes[strtolower($key)] = $value;
        }

        return $scopes;
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