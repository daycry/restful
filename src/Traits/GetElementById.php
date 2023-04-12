<?php

declare(strict_types=1);

namespace Daycry\RestFul\Traits;

use CodeIgniter\Entity\Entity;
use CodeIgniter\Model;

trait GetElementById
{
    /**
     * Returns relational object by Id
     */
    public function getById(Model $class, string $element)
    {
        return $class->where('id', $element)->first();
    }
}
