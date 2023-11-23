<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

use Daycry\RestFul\Entities\Endpoint;
use Daycry\RestFul\Exceptions\AuthenticationException;
use Daycry\RestFul\Validators\AccessToken;
use Daycry\Exceptions\Interfaces\BaseExceptionInterface;

trait Authenticable
{
    /**
     * Checks user validation
     * to see if the user has a specific validation type.
     *
     */
    public function doLogin(?Endpoint $endpoint = null)
    {
        helper('auth');

        $strictApiAndAuth = service('settings')->get('RestFul.strictApiAndAuth');
        $alias = (isset($endpoint->auth) && $endpoint->auth) ? $endpoint->auth : service('settings')->get('RestFul.defaultAuth');

        $user = false;

        try {
            if(AccessToken::isEnabled($endpoint)) {
                AccessToken::check($endpoint);
                $user = auth()->loggedIn();
                if(!$user) {
                    throw AuthenticationException::forInvalidAccessToken();
                }
            }

        } catch(BaseExceptionInterface $ex) {
            if($strictApiAndAuth || !$alias) {
                throw $ex;
            }
        }

        try {
            if($alias) {
                $authenticator = auth($alias);
                $authenticator->authenticate();
            }

        } catch(BaseExceptionInterface $ex) {
            if($strictApiAndAuth || (!$strictApiAndAuth && !$user)) {
                throw $ex;
            }
        }
    }
}
