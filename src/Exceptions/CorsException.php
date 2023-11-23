<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use Daycry\Exceptions\Exceptions\RuntimeException;

class CorsException extends RuntimeException
{
    protected $code = 204;

    public static function forNocontent(): self
    {
        return new self(lang('RestFul.noContent'));
    }
}
