<?php

namespace Daycry\RestFul\Validators;

use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

;
use Daycry\RestFul\Exceptions\AuthorizationException;

class Blacklist
{
    public static function check(ResponseInterface &$response)
    {
        helper('checkIp');

        $request = Services::request();

        $found = checkIp($request->getIPAddress(), service('settings')->get('RestFul.ipBlacklist'));
        if ($found) {
            $response->setStatusCode(401, lang('RestFul.ipDenied'));
            throw AuthorizationException::forIpDenied();
        }
    }
}
