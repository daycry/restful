<?php

declare(strict_types=1);


use Daycry\RestFul\Auth;

if (! function_exists('auth')) {
    /**
     * Provides convenient access to the main Auth class
     * for CodeIgniter RestFul.
     *
     * @param string|null $alias Authenticator alias
     */
    function auth(?string $alias = null): Auth
    {
        /** @var Auth $auth */
        $auth = service('auth');

        return $auth->setAuthenticator($alias);
    }
}
