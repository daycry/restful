<?php

declare(strict_types=1);

namespace Tests\Authenticators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AuthFilter;

/**
 * @internal
 */
final class NoAuthenticatorTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testNoAuthSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => null]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
