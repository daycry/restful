<?php

namespace Tests\Support\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Example extends ResourceController
{
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
