<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use CodeIgniter\HTTP\Exceptions\HTTPException;
use Daycry\RestFul\Exceptions\RuntimeException;

class AuthorizationException extends RuntimeException
{
    protected $code = 401;

    public static function forUnauthorized(): self
    {
        return new self(lang('RestFul.notEnoughPrivilege'));
    }
}
