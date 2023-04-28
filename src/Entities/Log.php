<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;

class Log extends Entity
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'authorized' => '?intBol'
    ];
}
