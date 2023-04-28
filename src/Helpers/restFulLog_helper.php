<?php

declare(strict_types=1);

use Daycry\RestFul\Libraries\Logger;

if (! function_exists('restFulLog')) {

    function restFulLog(): Logger
    {
        /** @var Logger $logger */
        $logger = service('log');

        return $logger;
    }
}
