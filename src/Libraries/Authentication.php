<?php

declare(strict_types=1);

namespace Daycry\RestFul\Libraries;

use Daycry\RestFul\Config\RestFul;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;

class Authentication
{
    /**
     * Instantiated Authenticator objects,
     * stored by Authenticator alias.
     *
     * @var array<string, AuthenticatorInterface> [Authenticator_alias => Authenticator_instance]
     */
    protected array $instances = [];

    protected ?UserModel $userProvider = null;
    protected RestFul $config;

    /**
     * Returns an instance of the specified Authenticator.
     *
     * You can pass 'default' as the Authenticator and it
     * will return an instance of the first Authenticator specified
     * in the RestFul config file.
     *
     * @param string|null $alias Authenticator alias
     *
     * @throws AuthenticationException
     */
    public function factory(?string $alias = null): ?AuthenticatorInterface
    {
        // Determine actual Authenticator alias
        $alias ??= service('settings')->get('RestFul.defaultAuth');
        if(!$alias) {
            return null;
        }

        // Return the cached instance if we have it
        if (! empty($this->instances[$alias])) {
            return $this->instances[$alias];
        }

        // Otherwise, try to create a new instance.
        if (! array_key_exists($alias, service('settings')->get('RestFul.authenticators'))) {
            throw AuthenticationException::forUnknownAuthenticator($alias);
        }

        $className = service('settings')->get('RestFul.authenticators')[$alias];

        assert($this->userProvider !== null, 'You must set $this->userProvider.');

        $this->instances[$alias] = new $className($this->userProvider);

        return $this->instances[$alias];
    }

    /**
     * Sets the User provider to use
     *
     * @return $this
     */
    public function setProvider(UserModel $provider): self
    {
        $this->userProvider = $provider;

        return $this;
    }
}
