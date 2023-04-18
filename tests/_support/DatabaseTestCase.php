<?php

declare(strict_types=1);

namespace Tests\Support;

use Daycry\RestFul\Config\RestFul;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * @internal
 */
abstract class DatabaseTestCase extends TestCase
{
    use DatabaseTestTrait;

    protected $namespace = '\Daycry\RestFul';

    /**
     * Auth Table names
     */
    protected array $tables;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var RestFul $restFulConfig */
        $restFulConfig   = config('RestFul');
        $this->tables = $restFulConfig->tables;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
