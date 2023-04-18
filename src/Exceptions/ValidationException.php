<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use RuntimeException;

class ValidationException extends RuntimeException
{
    public static function validationtMethodParamsError($param)
    {
        $parser = \Config\Services::parser();
        return new self($parser->setData(array( 'param' => $param ))->renderString(lang('RestFul.invalidParamsForMethod')));
    }
}
