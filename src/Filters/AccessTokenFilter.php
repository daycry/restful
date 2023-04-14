<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Exceptions\BaseException;
use Daycry\RestFul\Entities\Endpoint;

/**
 * Ajax Filter.
 *
 * @param array|null $arguments
 */
class AccessTokenFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['checkEndpoint','checkIp','auth']);

        $accessTokenEnabled = service('settings')->get('RestFul.accessTokenEnabled');
        $strictApiAndAuth = service('settings')->get('RestFul.strictApiAndAuth');
        $alias = service('settings')->get('RestFul.defaultAuth');

        /** @var Endpoint $endpoint */
        $endpoint = checkEndpoint();
        if ($endpoint) {
            $accessTokenEnabled = (($endpoint->access_key === null) || ($endpoint->access_key === 1)) ? $accessTokenEnabled : false;
            $alias = $endpoint->auth ?? $alias;
        }

        if ($accessTokenEnabled) {

            $actualAuth = (auth()->loggedIn()) ? auth()->user() : null;

            try {
                auth('token')->authenticate();
            } catch(BaseException $ex) {
                if(!$actualAuth || auth('token')->id() != $actualAuth->id) {
                    if($strictApiAndAuth) {
                        return Services::response()->setStatusCode(403, lang('RestFul.invalidCredentials'));
                    } else {
                        return Services::response()->setStatusCode(403, lang('RestFul.invalidAccessToken'));
                    }
                }
            }
        }
    }

    /**
     * We don't have anything to do here.
     *
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null): void
    {
        // Nothing required
    }
}
