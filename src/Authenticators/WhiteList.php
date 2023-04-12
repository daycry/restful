<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

class WhiteList extends Base implements AuthenticatorInterface
{
    public function __construct(UserModel $provider)
    {
        $this->method = 'whitelist';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        return $this->checkLogin('whitelist');
    }

    /**
     * @throws AuthenticationException
     */
    protected function attempt(string $authMethod, string $username, ?string $password = null): ?User
    {
        helper(['checkIp']);

        $found = checkIp($this->request->getIPAddress(), service('settings')->get('RestFul.ipWhitelist'));
        if (!$found) {
            throw AuthenticationException::forInvalidCredentials();
        }

        $user = $this->findUser($username, false);

        if (!$user) {
            $user = new User();
            $user->username = $this->request->getIPAddress();
        }

        return $user;
    }
}
