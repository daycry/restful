<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use Daycry\RestFul\Exceptions\RuntimeException;

class AuthorizationException extends RuntimeException
{
    public static $authorized = true;

    protected $code = 401;

    public static function forIpDenied()
    {
        self::$authorized = false;
        return new self(lang('RestFul.ipDenied'));
    }

    public static function forNotEnoughPrivilege()
    {
        self::$authorized = false;
        return new self(lang('RestFul.notEnoughPrivilege'));
    }
}
