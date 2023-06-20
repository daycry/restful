<?php

declare(strict_types=1);

use CodeIgniter\Config\Services;
use Daycry\RestFul\Models\ApiModel;
use Daycry\RestFul\Entities\Endpoint;
use Daycry\RestFul\Entities\Api;
use Daycry\RestFul\Entities\Controller;

if (! function_exists('checkEndpoint')) {
    /**
     * Provides an endpoint for the actual request
     */
    function checkEndpoint(): ?Endpoint
    {
        $apiModel = model(ApiModel::class);
        /** @var Api|null $api */
        $api = $apiModel->where('url', site_url())->first();

        if ($api) {
            $router = Services::router();
            $controllers = $api->getControllers($router->controllerName());
            /** @var Controller|null $controller */
            $controller = ($controllers) ? $controllers[0] : null;
            if ($controller) {
                $endpoints = $controller->getEndpoints($controller->controller . '::' . $router->methodName());
                return ($endpoints) ? $endpoints[0] : null;
            }
        }

        return null;
    }
}
