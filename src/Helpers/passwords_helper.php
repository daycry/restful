<?php

declare(strict_types=1);

use Daycry\RestFul\Libraries\Passwords;

if (! function_exists('passwords')) {
    /**
     * Provides Password class
     */
    function passwords(): Passwords
    {
        return service('passwords');
    }
}
