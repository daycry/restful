<?php

declare(strict_types=1);

use Daycry\RestFul\Auth;
use Config\Validation;
use Config\Services;
use Daycry\RestFul\Exceptions\ValidationException;

if (! function_exists('validation')) {
    /**
     * @param array $data
     * @param string $rules
     * @param Validation|null $config
     * @param bool $getShared
     * @param bool $filter
     * 
     * @throws ValidationException
     */
    function validation(array $data, string $rules, ?Validation $config = null, bool $getShared, bool $filter = false): void
    {
        $validator = Services::validation($config, $getShared);
        if (!$validator->run($data, $rules)) {
            throw new ValidationException();
        }

        if ($filter) {
            if ($data) {
                foreach ($data as $key => $value) {
                    if (!array_key_exists($key, $config->{$rules})) {
                        throw ValidationException::validationtMethodParamsError($key);
                    }
                }
            }
        }
    }
}
