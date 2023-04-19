<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Traits\Attemptable;

/**
 * BlackList Filter.
 *
 * @param array|null $arguments
 */
class BlackListFilter implements FilterInterface
{
    use Attemptable;

    public function before(RequestInterface $request, $arguments = null)
    {
        helper('checkIp');

        $ipBlacklistEnabled = service('settings')->get('RestFul.ipBlacklistEnabled');

        if ($ipBlacklistEnabled) {
            $found = checkIp($request->getIPAddress(), service('settings')->get('RestFul.ipBlacklist'));
            if ($found) {
                $this->registerAttempt();
                return Services::response()->setStatusCode(401, lang('RestFul.ipDenied'));
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
