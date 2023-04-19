<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

use Config\Services;
use Daycry\RestFul\Models\AttemptModel;

trait Attemptable
{
    public function registerAttempt(): void
    {
        $request = Services::request();
        if(service('settings')->get('RestFul.enableInvalidAttempts')) {
            $maxAttempts = service('settings')->get('RestFul.maxAttempts');
            $attemptModel = new AttemptModel();

            $attempt = $attemptModel->where('ip_address', $request->getIPAddress())->first();

            if ($attempt === null) {
                $attempt = [
                        'ip_address' => $request->getIPAddress(),
                        'attempts'      => 1,
                        'hour_started' => time(),
                    ];

                $attemptModel->save($attempt);
            } else {
                if ($attempt->attempts < $maxAttempts) {
                    $attempt->attempts = $attempt->attempts + 1;
                    $attempt->hour_started = time();
                    $attemptModel->save($attempt);
                }
            }
        }
    }

    public function removeAttempt(): void
    {
        $request = Services::request();
        if(service('settings')->get('RestFul.enableInvalidAttempts')) {
            $attemptModel = new AttemptModel();

            $attempt = $attemptModel->where('ip_address', $request->getIPAddress())->first();

            if ($attempt) {
                $attemptModel->delete($attempt->id, true);
            }
        }
    }
}
