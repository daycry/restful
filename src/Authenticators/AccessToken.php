<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Models\UserIdentityModel;
use Daycry\RestFul\Entities\User;

class AccessToken extends Base implements AuthenticatorInterface
{
    public const ID_TYPE_ACCESS_TOKEN = 'token';

    public function __construct(UserModel $provider)
    {
        $this->method = 'token';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        $accessTokenName = service('settings')->get('RestFul.accessTokenName');

        $key = $this->request->getHeaderLine($accessTokenName);
        $key = ($key) ? $key : $this->request->getGetPost($accessTokenName);
        $key = ($key) ? $key : $this->request->getVar($accessTokenName);

        if (!$key) {
            throw AuthenticationException::forInvalidAccessToken();
        }

        return $this->checkLogin($key);
    }

    /**
     * @throws AuthenticationException
     */
    protected function attempt(string $authMethod, string $username, ?string $password = null): ?User
    {
        helper(['checkIp']);

        $userIdentity = model(UserIdentityModel::class)->getIdentityBySecret('token', $username);

        if (!$userIdentity) {
            throw AuthenticationException::forInvalidAccessToken();
        }

        /**
         * If "is private key" is enabled, compare the ip address with the list
         * of valid ip addresses stored in the database
         */
        if ($userIdentity->is_private === true) {
            $found = checkIp($this->request->getIPAddress(), $userIdentity->ip_addresses);
            if (!$found) {
                throw AuthenticationException::forIpDenied();
            }
        }

        $this->user = $userIdentity->getUser();

        return $this->user;
    }
}
