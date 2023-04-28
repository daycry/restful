<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Exceptions\AuthorizationException;
use Daycry\RestFul\Interfaces\BaseException;
use Daycry\RestFul\Traits\Authenticable;

/**
 * Access Filter.
 *
 * @param array|null $arguments
 */
class AccessFilter implements FilterInterface
{
    use Authenticable;

    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['checkEndpoint', 'auth', 'restFulLog']);

        $logger = restFulLog();

        $enableCheckAccess = service('settings')->get('RestFul.enableCheckAccess');
        $alias = service('settings')->get('RestFul.defaultAuth');

        $scope = ($arguments) ? $arguments[0] : null;

        if ($enableCheckAccess) {
            try {
                if ($endpoint = checkEndpoint()) {
                    $alias = ($endpoint->auth) ? $endpoint->auth : $alias;
                    $scope = ($endpoint->scope) ? $endpoint->scope : $scope;
                }

                if($alias) {
                    $this->doLogin($endpoint);
                }

                if(!$alias || !auth($alias)->user() || ($scope && !auth($alias)->user()->can($scope))) {
                    //$this->registerAttempt();
                    throw AuthorizationException::forNotEnoughPrivilege();
                }

            } catch(BaseException $ex) {

                $logger->setAuthorized(false)
                    ->setResponseCode($ex->getCode())
                    ->save();
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
