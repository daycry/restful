<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Exceptions\BaseException;
use CodeIgniter\I18n\Time;
use Daycry\RestFul\Traits\Attemptable;

/**
 * Limit Filter.
 *
 * @param array|null $arguments
 */
class LimitFilter implements FilterInterface
{
    use Attemptable;

    public function before(RequestInterface $request, $arguments = null)
    {
        helper(['checkEndpoint', 'auth']);

        $enableLimit = service('settings')->get('RestFul.enableLimit');
        $alias = service('settings')->get('RestFul.defaultAuth');
        $limit = service('settings')->get('RestFul.requestLimit');
        $time = service('settings')->get('RestFul.timeLimit');


        if($enableLimit) {
            try {
                if(!auth($alias)->user()->getIdentity($alias)->ignore_limits) {
                    $router = Services::router();

                    if ($endpoint = checkEndpoint()) {
                        $limit = $endpoint->limit ?? $limit;
                        $time = $endpoint->time ?? $time;
                        $alias = $endpoint->auth ?? $alias;
                    }

                    switch (service('settings')->get('RestFul.limitMethod')) {
                        case 'IP_ADDRESS':
                            $api_key = $request->getIPAddress();
                            $limited_uri = 'ip-address:' . $request->getIPAddress();
                            break;

                        case 'USER':
                            $limited_uri = 'user:' . auth($alias)->user()->username;
                            break;

                        case 'METHOD_NAME':
                            $limited_uri = 'method-name:' . $router->controllerName() . '::' . $router->methodName();
                            break;

                        case 'ROUTED_URL':
                        default:
                            $limited_uri = 'uri:'.$request->getPath().':'.$request->getMethod(); // It's good to differentiate GET from PUT
                            break;
                    }

                    $limitModel = new \Daycry\RestFul\Models\LimitModel();
                    $result = $limitModel->where('uri', $limited_uri)->first();

                    // No calls have been made for this key
                    if ($result === null) {
                        $limitEntity = new \Daycry\RestFul\Entities\Limit();
                        $limitEntity->fill(
                            [
                                'user_id'      => auth($alias)->id(),
                                'uri'          => $limited_uri,
                                'count'        => 1
                            ]
                        );
                        $limitModel->save($limitEntity);

                    } elseif(Time::createFromFormat('Y-m-d H:i:s', $result->hour_started_at)->getTimestamp() < (time() - $time)) {
                        $result->hour_started_at = time();
                        $result->count = 1;
                        // Reset the started period and count
                        $limitModel->save($result);

                    } else {
                        if ($result->count >= $limit) {
                            $response = Services::response();
                            $now = Time::now();
                            $remaining = Time::createFromFormat('Y-m-d H:i:s', $result->hour_started_at)->getTimestamp() + $time - $now->getTimestamp();
                            $response->setStatusCode(429, lang('RestFul.invalidAttemptsLimit'));
                            $response->setHeader('X-RATE-LIMIT-RESET', (string)$remaining);
                            return $response;
                        }

                        // Increase the count by one
                        $result->count = $result->count + 1;
                        $limitModel->save($result);
                    }
                }
            } catch(BaseException $ex) {
                $this->registerAttempt();
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
