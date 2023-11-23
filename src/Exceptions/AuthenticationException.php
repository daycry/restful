<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use Daycry\Exceptions\Exceptions\RuntimeException;

class AuthenticationException extends RuntimeException
{
    public static $authorized = true;

    protected $code = 403;

    /**
     * @param string $alias Authenticator alias
     */
    public static function forUnknownAuthenticator(?string $alias): self
    {
        return new self(lang('RestFul.unknownAuthenticator', [$alias]));
    }

    public static function forInvalidLibraryImplementation(): self
    {
        return new self(lang('RestFul.invalidLibraryImplementation'));
    }

    public static function forUnknownUserProvider(): self
    {
        return new self(lang('RestFul.unknownUserProvider'));
    }

    public static function forInvalidCredentials(): self
    {
        self::$authorized = false;
        return new self(lang('RestFul.invalidCredentials'));
    }

    public static function forNoPassword(): self
    {
        self::$authorized = false;
        return new self(lang('RestFul.noPassword'));
    }

    public static function forInvalidAccessToken(): self
    {
        self::$authorized = false;
        return new self(lang('RestFul.invalidAccessToken'));
    }

    public static function forIpDenied(): self
    {
        return new self(lang('RestFul.ipDenied'));
    }


}
