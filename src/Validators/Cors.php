<?php

namespace Daycry\RestFul\Validators;

use CodeIgniter\HTTP\RequestInterface;
use Config\Services;
use CodeIgniter\Config\Factories;
use CodeIgniter\HTTP\ResponseInterface;
use Daycry\RestFul\Exceptions\CorsException;

class Cors
{
    public static function check(ResponseInterface &$response)
    {
        $request = Services::request();

        if ($request->getMethod() === 'options') {
            $response->setStatusCode(204, lang('RestFul.noContent'));
            throw CorsException::forNocontent();
        }

        // Convert the config items into strings
        $allowedCorsHeaders = implode(', ', service('settings')->get('RestFul.allowedCorsHeaders'));
        $allowedCorsMethods = implode(', ', service('settings')->get('RestFul.allowedCorsMethods'));


        if (service('settings')->get('RestFul.allowAnyCorsDomain')) {
            $response->setHeader('Access-Control-Allow-Origin', '*');
        } else {
            $origin = $request->getHeaderLine('origin');

            $allowedCorsOrigins = service('settings')->get('RestFul.allowedCorsOrigins');

            // If the origin domain is in the allowed_cors_origins list, then add the Access Control headers
            if (self::_isCors($request) && in_array($origin, $allowedCorsOrigins)) {
                $response->setHeader('Access-Control-Allow-Origin', $origin);
            }
        }

        $response->setHeader('Access-Control-Allow-Headers', $allowedCorsHeaders);
        $response->setHeader('Access-Control-Allow-Methods', $allowedCorsMethods);

        $response->setHeader('Access-Control-Expose-Headers', implode(', ', service('settings')->get('RestFul.exposedCorsHeaders')));

        if (service('settings')->get('RestFul.corsMaxAge') !== null) {
            $response = $response->setHeader('Access-Control-Max-Age', (string) service('settings')->get('RestFul.corsMaxAge'));
        }

        if (service('settings')->get('RestFul.supportsCredentials')) {
            $response = $response->setHeader('Access-Control-Allow-Credentials', 'true');
        }

    }

    private static function _isCors(RequestInterface $request)
    {
        return $request->hasHeader('origin') && !self::_isSameHost($request);
    }

    private static function _isSameHost(RequestInterface $request): bool
    {
        return $request->getHeaderLine('origin') === Factories::config('App')->baseURL;
    }
}
