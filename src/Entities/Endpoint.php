<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;

class Endpoint extends Entity
{
    /**
     * @var string[]
     * @phpstan-var list<string>
     * @psalm-var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'checked_at'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'           => '?integer',
        'access_token' => '?intBol',
        'log'          => '?intBol',
        'limit'        => '?integer'
    ];

    protected $castHandlers = [
        'intBol' => \Daycry\RestFul\Entities\Cast\IntBoolCast::class
    ];
}
