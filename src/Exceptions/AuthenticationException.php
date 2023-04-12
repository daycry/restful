<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use CodeIgniter\HTTP\Exceptions\HTTPException;
use Daycry\RestFul\Exceptions\RuntimeException;

class AuthenticationException extends RuntimeException
{
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
        return new self(lang('RestFul.invalidCredentials'));
    }

    public static function forNoPassword(): self
    {
        return new self(lang('RestFul.noPassword'));
    }

    public static function forInvalidAccessToken(): self
    {
        return new self(lang('RestFul.invalidAccessToken'));
    }

    public static function forIpDenied(): self
    {
        return new self(lang('RestFul.ipDenied'));
    }


}
