<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

/**
 * @internal
 */
final class CorsTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected array $domains = ['https://test-cors.local'];

    public function testCorsAllowAnyDomain(): void
    {
        $this->inkectMockAttributes(['checkCors' => true, 'allowAnyCorsDomain' => true]);

        $this->withHeaders([
            'Origin' => $this->domains[0]
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testCorsAllowCustomDomain(): void
    {
        $this->inkectMockAttributes(
            [
                'checkCors' => true,
                'allowAnyCorsDomain' => false,
                'allowedCorsOrigins' => $this->domains
            ]
        );

        $this->withHeaders([
            'Origin' => $this->domains[0]
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
        $result->assertHeader('Access-Control-Allow-Origin', $this->domains[0]);
    }

    public function testCorsAllowCustomDomainError(): void
    {
        $this->inkectMockAttributes(
            [
                'checkCors' => true,
                'allowAnyCorsDomain' => false,
                'allowedCorsOrigins' => $this->domains
            ]
        );

        $this->withHeaders([
            'Origin' => 'https://test-cors1.local'
        ]);

        $result = $this->call('get', 'example');

        $result->assertHeaderMissing('Access-Control-Allow-Origin');
        $result->assertHeader('Access-Control-Allow-Credentials');
    }

    public function testCorsOptionsMethodError(): void
    {
        $this->inkectMockAttributes(
            [
                'checkCors' => true,
                'allowAnyCorsDomain' => false,
                'allowedCorsOrigins' => $this->domains
            ]
        );

        $result = $this->call('options', 'example');

        $result->assertStatus(204);
        $this->assertSame(lang('RestFul.noContent'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
