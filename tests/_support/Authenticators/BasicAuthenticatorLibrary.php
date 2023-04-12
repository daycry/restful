<?php

namespace Tests\Support\Authenticators;

use Daycry\RestFul\Interfaces\LibraryAuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

class BasicAuthenticatorLibrary implements LibraryAuthenticatorInterface
{
    protected UserModel $provider;

    public function __construct(UserModel $provider)
    {
        $this->provider = $provider;
    }

    public function check(string $username, ?string $password = null): ?User
    {
        helper('passwords');

        if ($username != 'daycry') {
            throw AuthenticationException::forInvalidCredentials();
        }

        /** @var User $user */
        $user = $this->provider->where('username', $username)->first();

        if (!$user) {
            throw AuthenticationException::forInvalidCredentials();
        }

        $identity = $user->getIdentity('basic');
        if (!$password) {
            throw AuthenticationException::forNoPassword();
        }


        if (!passwords()->verify($password, $identity->secret)) {
            throw AuthenticationException::forInvalidCredentials();
        }

        return $user;
    }
}
