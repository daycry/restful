<?php

declare(strict_types=1);

namespace Daycry\RestFul\Config;

use Daycry\RestFul\Filters\AccessFilter;

class Registrar
{
    /**
     * Register filters.
     */
    public static function Filters(): array
    {
        return [
            'aliases' => [
                'access'      => [
                    AccessFilter::class,
                ]
            ],
        ];
    }
}
