<?php

declare(strict_types=1);

namespace Daycry\RestFul\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Config\Services;

/**
 * Ajax Filter.
 *
 * @param array|null $arguments
 */
class AjaxFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $ajaxOnly = service('settings')->get('RestFul.ajaxOnly');

        if ($request->isAJAX() === false && $ajaxOnly) {
            return Services::response()->setStatusCode(403, lang('RestFul.ajaxOnly'));
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
