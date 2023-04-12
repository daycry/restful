<?php

declare(strict_types=1);

namespace Daycry\RestFul\Authenticators;

use CodeIgniter\Config\Services;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Interfaces\LibraryAuthenticatorInterface;

abstract class Base
{
    protected $request = true;

    protected string $method;

    protected UserModel $provider;

    protected ?User $user = null;

    public function __construct()
    {
        $this->request = Services::request();
        $this->provider = new $this->provider();
    }

    public function loggedIn(): bool
    {
        if (! empty($this->user)) {
            return true;
        }

        return false;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    abstract protected function attempt(string $authMethod, string $username, ?string $password = null);

    /**
     * Check if the user is logged in
     *
     * @access protected
     * @param string|null $username The user's name
     * @param string|null $password The user's password
     */
    protected function checkLogin(?string $username = null, ?string $password = null)
    {
        if (empty($username)) {
            $this->forceLogin();
        }

        $authSource = service('settings')->get('RestFul.authSource');
        $authMethod = \strtolower($this->method);

        if ($authSource === 'library') {
            log_message('debug', "Performing Library authentication for $username");

            return $this->_performLibraryAuth($username, $password);
        }

        return $this->attempt($authMethod, $username, $password);
    }

    /**
     * Force logging in by setting the WWW-Authenticate header
     *
     * @access protected
     * @param string $nonce A server-specified data string which should be uniquely generated each time
     * @return void
     */
    protected function forceLogin($nonce = '')
    {
        $rest_auth = \strtolower($this->method);
        $rest_realm = service('settings')->get('RestFul.restRealm');

        //if (service('settings')->get('RestFul.strictAccessTokenAndAuth') === true) {
        if (Services::request()->getUserAgent()->isBrowser()) {
            // @codeCoverageIgnoreStart
            if (strtolower($rest_auth) === 'basic') {
                // See http://tools.ietf.org/html/rfc2617#page-5
                header('WWW-Authenticate: Basic realm="' . $rest_realm . '"');
            } elseif (strtolower($rest_auth) === 'digest') {
                // See http://tools.ietf.org/html/rfc2617#page-18
                header(
                    'WWW-Authenticate: Digest realm="' . $rest_realm
                    . '", qop="auth", nonce="' . $nonce
                    . '", opaque="' . md5($rest_realm) . '"'
                );
            }
            // @codeCoverageIgnoreEnd
        }

        throw AuthenticationException::forInvalidCredentials();
    }

    protected function _performLibraryAuth(string $username, ?string $password = null)
    {
        $authLibraryClass = service('settings')->get('RestFul.libraryCustomAuthenticators');

        if (!isset($authLibraryClass[ $this->method ]) || !\class_exists($authLibraryClass[ $this->method ])) {
            throw AuthenticationException::forUnknownAuthenticator($this->method);
        }

        $authLibraryClass = new $authLibraryClass[ $this->method ]($this->provider);

        if ((!$authLibraryClass instanceof LibraryAuthenticatorInterface)) {
            throw AuthenticationException::forInvalidLibraryImplementation();
        }

        if (\is_callable([ $authLibraryClass, 'check' ])) {
            /** @var User $user */
            $user = $authLibraryClass->{'check'}($username, $password);
            $this->user = $user;

            return $user;
        }
    }

    protected function findUser(string $username, bool $exception = true): ?User
    {
        $user = $this->provider->where('username', $username)->first();

        if (!$user && $exception) {
            throw AuthenticationException::forInvalidCredentials();
        }

        return $user;
    }
}
