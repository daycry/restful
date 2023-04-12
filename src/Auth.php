<?php

declare(strict_types=1);

namespace Daycry\RestFul;

use Daycry\RestFul\Libraries\Authentication;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Models\UserModel;

/**
 * @method Result    attempt(array $credentials)
 * @method Result    check(array $credentials)
 * @method bool      checkAction(string $token, string $type) [Session]
 * @method User|null getUser()
 * @method bool      loggedIn()
 * @method bool      login(User $user)
 * @method void      loginById($userId)
 * @method bool      logout()
 * @method void      recordActiveDate()
 * @method $this     remember(bool $shouldRemember = true)    [Session]
 */
class Auth
{
    protected Authentication $authenticate;

    /**
     * The Authenticator alias to use for this request.
     */
    protected ?string $alias = null;

    protected ?UserModel $userProvider = null;

    public function __construct(Authentication $authenticate)
    {
        $this->authenticate = $authenticate->setProvider($this->getProvider());
    }

    /**
     * Sets the Authenticator alias that should be used for this request.
     *
     * @return $this
     */
    public function setAuthenticator(?string $alias = null): self
    {
        if (! empty($alias)) {
            $this->alias = $alias;
        }

        return $this;
    }

    /**
     * Returns the current authentication class.
     */
    public function getAuthenticator(): AuthenticatorInterface
    {
        return $this->authenticate
            ->factory($this->alias);

    }

    /**
     * Returns the current user, if logged in.
     */
    public function user(): ?User
    {
        return $this->getAuthenticator()->loggedIn()
            ? $this->getAuthenticator()->getUser()
            : null;
    }

    /**
     * Returns the current user's id, if logged in.
     *
     * @return int|string|null
     */
    public function id()
    {
        return ($user = $this->user())
            ? $user->id
            : null;
    }

    public function authenticate()
    {
        return $this->getAuthenticator()->check();
    }

    /**
     * Returns the Model that is responsible for getting users.
     *
     * @throws AuthenticationException
     */
    public function getProvider(): UserModel
    {
        if ($this->userProvider !== null) {
            return $this->userProvider;
        }

        $userProvider = service('settings')->get('RestFul.userProvider');

        if (!$userProvider) {
            throw AuthenticationException::forUnknownUserProvider();
        }

        $className          = $userProvider;
        $this->userProvider = new $className();

        return $this->userProvider;
    }
}
