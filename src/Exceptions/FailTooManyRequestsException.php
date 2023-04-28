<?php

namespace Daycry\RestFul\Exceptions;

use Daycry\RestFul\Exceptions\RuntimeException;

class FailTooManyRequestsException extends RuntimeException
{
    protected $code = 429;

    public static $authorized = true;

    public static function forApiKeyLimit(string $key)
    {
        self::$authorized = false;
        $parser = \Config\Services::parser();
        return new self($parser->setData(array( 'key' => $key ))->renderString(lang('Rest.textRestApiKeyTimeLimit')));
    }

    public static function forInvalidAttemptsLimit()
    {
        self::$authorized = false;
        return new self(lang('RestFul.invalidAttemptsLimit'));
    }

    public static function forIpAddressTimeLimit()
    {
        self::$authorized = false;
        return new self(lang('RestFul.textRestIpAddressTimeLimit'));
    }
}
