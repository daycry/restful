<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use Daycry\Exceptions\Exceptions\RuntimeException;

class ValidationException extends RuntimeException
{
    public static $authorized = true;

    public static function validationtMethodParamsError($param)
    {
        self::$authorized = false;
        $parser = \Config\Services::parser();
        return new self($parser->setData(array( 'param' => $param ))->renderString(lang('RestFul.invalidParamsForMethod')));
    }

    public static function validationData()
    {
        self::$authorized = false;
        return new self('');
    }
}
