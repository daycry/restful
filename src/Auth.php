<?php

declare(strict_types=1);

namespace Daycry\RestFul;

use Daycry\RestFul\Libraries\Authentication;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Interfaces\BaseException;

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
    /**
     * The current version
     */
    public const RESTFUL_VERSION = '1.0.0';

    protected Authentication $authenticate;

    /**
     * The Authenticator alias to use for this request.
     */
    public ?string $alias = null;

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
    public function getAuthenticator(): ?AuthenticatorInterface
    {
        return $this->authenticate
            ->factory($this->alias);

    }

    /**
     * Returns the current user, if logged in.
     */
    public function user(): ?User
    {
        if($this->getAuthenticator()) {
            if($this->getAuthenticator()->loggedIn()) {
                return $this->getAuthenticator()->getUser();
            } else {
                return $this->authenticate();
            }
        }

        return null;
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
        return $this->getAuthenticator()
            ? $this->getAuthenticator()->check()
            : null;
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

    /**
     * Provide magic function-access to Authenticators to save use
     * from repeating code here, and to allow them have their
     * own, additional, features on top of the required ones,
     * like "remember-me" functionality.
     *
     * @param string[] $args
     *
     * @throws AuthenticationException
     */
    public function __call(string $method, array $args)
    {
        try {
            $authenticate = $this->getAuthenticator();

            if ($authenticate && method_exists($authenticate, $method)) {
                return $authenticate->{$method}(...$args);
            }
        } catch(BaseException $ex) {
            return false;
        }
    }
}
