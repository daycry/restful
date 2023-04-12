<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;
use Daycry\JWT\JWT;

class Bearer extends Base implements AuthenticatorInterface
{
    public function __construct(UserModel $provider)
    {
        $this->method = 'bearer';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        // Returns HTTP_AUTHENTICATION don't exist
        $http_auth = $this->request->getHeaderLine('Authentication') ?: $this->request->getHeaderLine('Authorization');

        $username = null;
        if ($http_auth !== null) {
            // If the authentication header is set as bearer, then extract the token from
            if (strpos(strtolower($http_auth), 'bearer') === 0) {
                $username = substr($http_auth, 7);
            }
        }

        return $this->checkLogin($username);
    }

    protected function attempt(string $authMethod, string $username, ?string $password = null): ?User
    {
        $claims = (new JWT())->decode($username);

        $user = $this->findUser($claims->all()['uid']);
        $this->user = $user;

        return $user;
    }
}
