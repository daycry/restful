<?php

namespace Daycry\RestFul\Validators;

use Daycry\RestFul\Entities\Endpoint;

class AccessToken
{
    public static function check(?Endpoint $endpoint)
    {
        helper('auth');

        if (self::isEnabled($endpoint)) {
            auth('token')->authenticate();
        }
    }

    public static function isEnabled(?Endpoint $endpoint): bool
    {
        $accessTokenEnabled = service('settings')->get('RestFul.accessTokenEnabled');

        if ($endpoint) {
            $accessTokenEnabled = (($endpoint->access_key === null) || ($endpoint->access_key === 1)) ? $accessTokenEnabled : false;
        }

        return $accessTokenEnabled;
    }
}
