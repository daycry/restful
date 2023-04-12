<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\CorsFilter;

/**
 * @internal
 */
final class CorsTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'cors';
    protected mixed $classname = CorsFilter::class;
    protected array $domains = ['https://test-cors.local'];

    public function testCorsAllowAnyDomain(): void
    {
        $this->inkectMockAttributes(['allowAnyCorsDomain' => true]);

        $this->withHeaders([
            'Origin' => $this->domains[0]
        ]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    public function testCorsAllowCustomDomain(): void
    {
        $domain = $this->domains[0];

        $this->inkectMockAttributes(
            [
                'allowAnyCorsDomain' => false,
                'allowedCorsOrigins' => $this->domains
            ]
        );

        $this->withHeaders([
            'Origin' => $this->domains[0]
        ]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
        $result->assertHeader('Access-Control-Allow-Origin', $this->domains[0]);
    }

    public function testCorsAllowCustomDomainError(): void
    {
        $this->inkectMockAttributes(
            [
                'allowAnyCorsDomain' => false,
                'allowedCorsOrigins' => $this->domains
            ]
        );

        $this->withHeaders([
            'Origin' => 'https://test-cors1.local'
        ]);

        $result = $this->call('get', 'filter-route');

        $result->assertHeaderMissing('Access-Control-Allow-Origin');
        $result->assertHeader('Access-Control-Allow-Credentials');
    }

    public function testCorsOptionsMethodError(): void
    {
        $result = $this->call('options', 'filter-route');

        $result->assertStatus(204);
        $this->assertSame(lang('RestFul.noContent'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
