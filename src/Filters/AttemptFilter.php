<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Daycry\RestFul\Models\AttemptModel;
use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;
use Daycry\RestFul\Traits\Attemptable;

/**
 * Attempt Filter.
 *
 * @param array|null $arguments
 */
class AttemptFilter implements FilterInterface
{
    use Attemptable;

    public function before(RequestInterface $request, $arguments = null)
    {
        $enableInvalidAttempts = service('settings')->get('RestFul.enableInvalidAttempts');

        if ($enableInvalidAttempts) {
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
                    $response = Services::response();
                    $now = Time::now();
                    $remaining = $date->getTimestamp() + $timeBlocked - $now->getTimestamp();
                    $response->setStatusCode(429, lang('RestFul.invalidAttemptsLimit'));
                    $response->setHeader('X-RATE-LIMIT-RESET', (string)$remaining);
                    return $response;
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
