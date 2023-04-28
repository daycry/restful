<?php

namespace Daycry\RestFul\Validators;

use Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

;
use Daycry\RestFul\Exceptions\FailTooManyRequestsException;
use Daycry\RestFul\Models\AttemptModel;
use CodeIgniter\I18n\Time;

class Attempt
{
    public static function check(ResponseInterface &$response)
    {
        $request = Services::request();

        $maxAttempts = service('settings')->get('RestFul.maxAttempts');
        $timeBlocked = service('settings')->get('RestFul.timeBlocked');

        /** @var \Daycry\RestFul\Models\AttemptModel $attemptModel */
        $attemptModel = new AttemptModel();
        $attempt = $attemptModel->where('ip_address', $request->getIPAddress())->first();

        if ($attempt && $attempt->attempts >= $maxAttempts) {
            $date = Time::createFromFormat('Y-m-d H:i:s', $attempt->hour_started_at);
            if ($date->getTimestamp() <= (time() - $timeBlocked)) {
                $attemptModel->delete($attempt->id, true);
            } else {
                $now = Time::now();
                $remaining = $date->getTimestamp() + $timeBlocked - $now->getTimestamp();
                $response->setHeader('X-RATE-LIMIT-RESET', (string)$remaining);

                throw FailTooManyRequestsException::forInvalidAttemptsLimit();
            }
        }
    }
}
