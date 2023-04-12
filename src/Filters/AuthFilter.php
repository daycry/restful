<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Daycry\RestFul\Exceptions\BaseException;
use CodeIgniter\HTTP\Response;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Exceptions\AuthenticationException;

/**
 * Ajax Filter.
 *
 * @param array|null $arguments
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['checkEndpoint', 'auth']);

        $alias = service('settings')->get('RestFul.defaultAuth');

        try {
            if ($endpoint = checkEndpoint()) {
                $alias = ($endpoint->auth) ? $endpoint->auth : $alias;
            }

            if($alias) {
                $authenticator = auth($alias);
                $authenticator->authenticate();
            }

        } catch(BaseException $ex) {
            return Services::response()->setStatusCode($ex->getCode(), $ex->getMessage());
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
