<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;

class Attempt extends Entity
{
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
