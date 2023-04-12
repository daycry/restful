<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;

/**
 * BlackList Filter.
 *
 * @param array|null $arguments
 */
class WhiteListFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        helper('checkIp');

        $ipWhitelistEnabled = service('settings')->get('RestFul.ipWhitelistEnabled');

        if ($ipWhitelistEnabled) {
            $found = checkIp($request->getIPAddress(), service('settings')->get('RestFul.ipWhitelist'));
            if (!$found) {
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
