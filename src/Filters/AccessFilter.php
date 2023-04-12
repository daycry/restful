<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Exceptions\BaseException;

/**
 * Access Filter.
 *
 * @param array|null $arguments
 */
class AccessFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['checkEndpoint', 'auth']);

        $enableCheckAccess = service('settings')->get('RestFul.enableCheckAccess');
        $alias = service('settings')->get('RestFul.defaultAuth');
        $scope = ($arguments) ? $arguments[0] : null;
        if ($enableCheckAccess) {
            try {
                if ($endpoint = checkEndpoint()) {
                    $alias = $endpoint->auth ?? $alias;
                    $scope = $endpoint->scope ?? $scope;
                }

                if(!$alias || !auth($alias)->user() || ($scope && !auth($alias)->user()->can($scope))) {
                    return Services::response()->setStatusCode(401, lang('RestFul.notEnoughPrivilege'));
                }

            } catch(BaseException $ex) {
                return Services::response()->setStatusCode($ex->getCode(), $ex->getMessage());
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
