<?php

namespace Daycry\RestFul\Validators;

use Daycry\RestFul\Config\Services;
use CodeIgniter\HTTP\ResponseInterface;

class Cors
{
    public static function check(ResponseInterface &$response)
    {
        $request = Services::request();
        $cors = Services::cors();

        if ($cors->isPreflightRequest($request)) {
            $response = $cors->handlePreflightRequest($request);
            $response = $cors->varyHeader($response, 'Access-Control-Request-Method');

            return $response;
        }

        if ($request->getMethod() === 'options') {
            $response = $cors->varyHeader($response, 'Access-Control-Request-Method');
        }

        if (! $response->hasHeader('Access-Control-Allow-Origin')) {
            // Add the CORS headers to the Response
            $response = $cors->addActualRequestHeaders($response, $request);
        }
    }
}
