<?php

namespace markhuot\CraftQL\Models;

use Craft;
use craft\db\ActiveRecord;
use Firebase\JWT\ExpiredException;
use GraphQL\Error\UserError;
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
     * Whether the token is for an anonymous user (pre-auth/token)
     *
     * @var bool
     */
    private $anonymous = false;

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
        if (empty($token)) {
            $token = static::tokenForSession();
        }

        else {
            $token = static::tokenForString($token);
        }

        if (!$token) {
            $token = static::anonymous();
        }

        return $token;
    }

    public static function tokenForSession() {
        if ($user = Craft::$app->getUser()->getIdentity()) {
            return Token::forUser($user);
        }

        return false;
    }

    public static function tokenForString($token) {
        // If the token matches a JWT format
        if (preg_match('/[^.]+\.[^.]+\.[^.]+/', $token)) {
            try {
                $tokenData = CraftQL::getInstance()->jwt->decode($token);
            }
            catch (ExpiredException $e) {
                throw new UserError('The token has expired');
            }
            $user = \craft\elements\User::find()->id($tokenData->id)->one();
            $token = Token::forUser($user);
            return $token;
        }

        // If the token is in the database
        /** @var Token $token */
        if ($token = Token::find()->where(['token' => $token])->one()) {
            return $token;
        }

        return false;
    }

    /**
     * A generic admin token
     *
     * @return Token
     */
    public static function admin(): Token
    {
        return (new static)->makeAdmin();
    }

    /**
     * Turn the token in to an admin token
     *
     * @return $this
     */
    public function makeAdmin() {
        $this->admin = true;
        return $this;
    }

    /**
     * A generic anonymous token
     *
     * @return Token
     */
    public static function anonymous(): Token {
        return (new static)->makeAnonymous();
    }

    /**
     * Turn the token in to an anonymous token
     *
     * @return $this
     */
    public function makeAnonymous() {
        $this->anonymous = true;
        return $this;
    }

    /**
     * Returns a token with the user permissions translated over in to token permissions
     *
     * @param \craft\elements\User $user
     * @return Token
     */
    public static function forUser(\craft\elements\User $user): Token
    {
        $token = new static;
        $token->setUser($user);

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