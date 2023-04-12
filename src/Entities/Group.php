<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;

class Group extends Entity
{
    /**
     * @var string[]
     * @phpstan-var list<string>
     * @psalm-var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'           => '?integer',
        'scopes'       => 'serialize'
    ];

    protected $castHandlers = [
        'serialize' => \Daycry\RestFul\Entities\Cast\SerializeCast::class
    ];
}
