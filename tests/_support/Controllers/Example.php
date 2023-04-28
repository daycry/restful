<?php

namespace Tests\Support\Controllers;

use CodeIgniter\RESTful\ResourceController;
use Daycry\RestFul\RestFul;
use Daycry\RestFul\Traits\Authenticable;

class Example extends ResourceController
{
    use RestFul;
    use Authenticable;

    public function read()
    {
        return $this->respond("Passed");
    }

    public function write()
    {
        return $this->respond("Passed");
    }

    public function noread()
    {
        return $this->respond("Passed");
    }
}
