<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use Daycry\Exceptions\Exceptions\RuntimeException;
use Config\Services;

class ForbiddenException extends RuntimeException
{
    public static $authorized = true;

    protected $code = 403;

    /**
     * @codeCoverageIgnore
     */
    public static function forUnsupportedProtocol()
    {
        self::$authorized = false;
        return new self(lang('RestFul.unsupportedProtocol'));
    }

    public static function forOnlyAjax()
    {
        self::$authorized = false;
        return new self(lang('RestFul.ajaxOnly'));
    }

    public static function forInvalidMethod($method)
    {
        self::$authorized = false;
        $parser = Services::parser();
        return new self($parser->setData(array( 'method' => $method ))->renderString(lang('RestFul.invalidMethod')));
    }
}
