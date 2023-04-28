<?php

namespace Daycry\RestFul\Validators;

use Daycry\RestFul\Entities\Endpoint;

class AccessToken
{
    public static function check(?Endpoint $endpoint)
    {
        helper('auth');

        $accessTokenEnabled = service('settings')->get('RestFul.accessTokenEnabled');

        if ($endpoint) {
            $accessTokenEnabled = (($endpoint->access_key === null) || ($endpoint->access_key === 1)) ? $accessTokenEnabled : false;
        }

        if ($accessTokenEnabled) {
            auth('token')->authenticate();
        }
    }
}
