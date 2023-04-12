<?php

declare(strict_types=1);

namespace Daycry\RestFul\Config;

use Daycry\RestFul\Filters\AjaxFilter;
use Daycry\RestFul\Filters\CorsFilter;
use Daycry\RestFul\Filters\AttemptFilter;
use Daycry\RestFul\Filters\BlackListFilter;
use Daycry\RestFul\Filters\WhiteListFilter;
use Daycry\RestFul\Filters\AuthFilter;
use Daycry\RestFul\Filters\AccessTokenFilter;
use Daycry\RestFul\Filters\AccessFilter;
use Daycry\RestFul\Filters\LimitFilter;

class Registrar
{
    /**
     * Register filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'chain'      => [
                    AjaxFilter::class,
                    CorsFilter::class,
                    AttemptFilter::class,
                    BlackListFilter::class,
                    WhiteListFilter::class,
                    AuthFilter::class,
                    AccessTokenFilter::class,
                    AccessFilter::class,
                    LimitFilter::class
                ]
            ],
        ];
    }
}
