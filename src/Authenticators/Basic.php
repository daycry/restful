<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use Daycry\RestFul\Interfaces\AuthenticatorInterface;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

class Basic extends Base implements AuthenticatorInterface
{
    public function __construct(UserModel $provider)
    {
        $this->method = 'basic';
        $this->provider = $provider;
        parent::__construct();
    }

    public function check(): ?User
    {
        $username = $this->request->getServer('PHP_AUTH_USER');
        $http_auth = $this->request->getHeaderLine('Authentication') ?: $this->request->getHeaderLine('Authorization');

        $password = null;
        if ($username !== null) {
            // @codeCoverageIgnoreStart
            $password = $this->request->getServer('PHP_AUTH_PW');
        // @codeCoverageIgnoreEnd
        } elseif ($http_auth !== null) {
            // If the authentication header is set as basic, then extract the username and password from
            // HTTP_AUTHORIZATION e.g. my_username:my_password. This is passed in the .htaccess file
            if (strpos(strtolower($http_auth), 'basic') === 0) {
                // Search online for HTTP_AUTHORIZATION workaround to explain what this is doing
                list($username, $password) = explode(':', base64_decode(substr($this->request->getHeaderLine('authorization'), 6)));
            }
        }

        $password = $password ?? null;

        return $this->checkLogin($username, $password);
    }

    /**
     * @throws AuthenticationException
     */
    protected function attempt(string $authMethod, string $username, ?string $password = null): ?User
    {
        helper(['passwords']);

        $user = $this->findUser($username);
        $identity = $user->getIdentity($authMethod);
        if (!$password) {
            throw AuthenticationException::forNoPassword();
        }

        if (!passwords()->verify($password, $identity->secret)) {

            $this->forceLogin();
        }

        $this->user = $user;

        return $user;
    }
}
