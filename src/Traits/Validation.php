<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

use Config\Validation as ValidationCOnfig;
use Daycry\RestFul\Exceptions\ValidationException;
use Config\Services;

trait Validation
{
    protected function validation(string $rules, $data = null, ?ValidationCOnfig $config = null, bool $getShared = true, bool $filter = false)
    {
        $config ??= config('Validation');
        $data ??= $this->content;

        $this->validator = Services::validation($config, $getShared);

        $content = json_decode(json_encode($data), true);
        if (!$this->validator->run($content, $rules)) {
            throw ValidationException::validationData();
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
