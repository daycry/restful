<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

class Session extends Base implements AuthenticatorInterface
{
    public function __construct(UserModel $provider)
    {
        $this->method = 'session';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        // Load library session of CodeIgniter
        $session = \Config\Services::session();

        // If false, then the user isn't logged in
        if (!$session->get(service('settings')->get('RestFul.authSource'))) {
            throw AuthenticationException::forInvalidCredentials();
        }

        return $this->checkLogin($session->get(service('settings')->get('RestFul.authSource')));
    }

    /**
     * @throws AuthenticationException
     */
    protected function attempt(string $authMethod, string $username, ?string $password = null): ?User
    {
        $user = $this->findUser($username);

        if (!$user) {
            throw AuthenticationException::forInvalidCredentials();
        }

        $this->user = $user;

        return $this->user;
    }
}
