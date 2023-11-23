<?php

namespace Daycry\RestFul\Validators;

use Daycry\RestFul\Entities\Endpoint;
use Config\Services;
use Daycry\RestFul\Models\LimitModel;
use CodeIgniter\I18n\Time;
use Daycry\RestFul\Exceptions\FailTooManyRequestsException;
use Daycry\Exceptions\Interfaces\BaseExceptionInterface;

class Limit
{
    public static function check(?Endpoint $endpoint)
    {
        helper('auth');

        $request = Services::request();
        $router = Services::router();

        $limit = service('settings')->get('RestFul.requestLimit');
        $time = service('settings')->get('RestFul.timeLimit');

        if ($endpoint) {
            $limit = ($endpoint->limit) ? $endpoint->limit : $limit;
            $time = ($endpoint->time) ? $endpoint->time : $time;
        }

        $ignoreLimits = false;
        $userId = null;

        if($userId = auth()->id()) {
            $ignoreLimits = auth()->user()->ignore_limits;
        }

        if(!$ignoreLimits) {
            switch (service('settings')->get('RestFul.limitMethod')) {
                case 'IP_ADDRESS':
                    $api_key = $request->getIPAddress();
                    $limited_uri = 'ip-address:' . $request->getIPAddress();
                    break;

                case 'USER':
                    $limited_uri = 'user:' . auth()->user()->username;
                    break;

                case 'METHOD_NAME':
                    $limited_uri = 'method-name:' . $router->controllerName() . '::' . $router->methodName();
                    break;

                case 'ROUTED_URL':
                default:
                    $limited_uri = 'uri:'.$request->getPath().':'.$request->getMethod(); // It's good to differentiate GET from PUT
                    break;
            }

            $limitModel = new LimitModel();
            $result = $limitModel->where('uri', $limited_uri)->first();

            if ($result === null) {
                $limitEntity = new \Daycry\RestFul\Entities\Limit();
                $limitEntity->fill(
                    [
                        'user_id'      => $userId,
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

                    throw FailTooManyRequestsException::forInvalidAttemptsLimit();
                }

                // Increase the count by one
                $result->count = $result->count + 1;
                $limitModel->save($result);
            }
        }
    }
}
