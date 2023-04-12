<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Config\Services;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Factories;

/**
 * Cors Filter.
 *
 * @param array|null $arguments
 */
class CorsFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $response = Services::response();

        if ($request->getMethod() === 'options') {
            $response->setStatusCode(204, lang('RestFul.noContent'));
            return $response;
        }
    }

    private function _isCors(RequestInterface $request)
    {
        return $request->hasHeader('origin') && !self::_isSameHost($request);
    }

    private function _isSameHost(RequestInterface $request): bool
    {
        return $request->getHeaderLine('origin') === Factories::config('App')->baseURL;
    }

    /**
     *
     * @param array|null $arguments
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        $allowedCorsHeaders = service('settings')->get('RestFul.allowedCorsHeaders');
        $allowedCorsMethods = service('settings')->get('RestFul.allowedCorsMethods');

        if ($this->_isCors($request)) {
            if (service('settings')->get('RestFul.allowAnyCorsDomain')) {
                $response->setHeader('Access-Control-Allow-Origin', '*');
            } else {
                $origin = $request->getHeaderLine('origin');

                $allowedCorsOrigins = service('settings')->get('RestFul.allowedCorsOrigins');

                // If the origin domain is in the allowed_cors_origins list, then add the Access Control headers
                if (in_array($origin, $allowedCorsOrigins)) {
                    $response->setHeader('Access-Control-Allow-Origin', $origin);
                }
            }

            $response->setHeader('Access-Control-Allow-Headers', $allowedCorsHeaders);
            $response->setHeader('Access-Control-Allow-Methods', $allowedCorsMethods);

            $forcedheaders = service('settings')->get('RestFul.forcedCorsHeaders');
            // If there are headers that should be forced in the CORS check, add them now
            if (is_array($forcedheaders)) {
                foreach ($forcedheaders as $header => $value) {
                    $response->setHeader($header, $value);
                }
            }

            return $response;
        }
    }
}
