<?php

declare(strict_types=1);

namespace Tests\Commands;

use Tests\Support\TestCase;
use CodeIgniter\Test\Filters\CITestStreamFilter;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

/**
 * @internal
 */
final class DiscoverTest extends TestCase
{
    use DatabaseTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected function setUp(): void
    {
        parent::setUp();

        CITestStreamFilter::registration();
        CITestStreamFilter::addOutputFilter();
        CITestStreamFilter::addErrorFilter();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        CITestStreamFilter::removeOutputFilter();
        CITestStreamFilter::removeErrorFilter();
    }

    public function testDiscover(): void
    {
        command('restful:discover');
        $this->assertStringContainsString('**** FINISHED. ****', CITestStreamFilter::$buffer);
    }
}
