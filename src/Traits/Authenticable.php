<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

use Daycry\RestFul\Entities\Endpoint;
use Daycry\RestFul\Validators\AccessToken;
use Daycry\RestFul\Interfaces\BaseException;

trait Authenticable
{
    /**
     * Checks user validation
     * to see if the user has a specific validation type.
     *
     */
    public function login(?Endpoint $endpoint = null)
    {
        helper('auth');

        $strictApiAndAuth = service('settings')->get('RestFul.strictApiAndAuth');
        $alias = (isset($endpoint->auth) && $endpoint->auth) ? $endpoint->auth : service('settings')->get('RestFul.defaultAuth');

        $user = false;

        try {
            AccessToken::check($endpoint);
            $user = auth()->user();
        } catch(BaseException $ex) {
            if($strictApiAndAuth || !$alias) {
                throw $ex;
            }
        }

        try {
            if($alias) {
                $authenticator = auth($alias);
                $authenticator->authenticate();
            }

        } catch(BaseException $ex) {
            if($strictApiAndAuth || (!$strictApiAndAuth && !$user)) {
                throw $ex;
            }
        }
    }
}
