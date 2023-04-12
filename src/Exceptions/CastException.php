<?php

declare(strict_types=1);

namespace Daycry\RestFul\Exceptions;

use CodeIgniter\Entity\Exceptions\CastException as CastExceptionCore;

/**
 * Cast Exception.
 */
class CastException extends CastExceptionCore
{
    public static function forInvalidObject()
    {
        return new static(lang('RestFul.invalidObjectForCast'));
    }
}
