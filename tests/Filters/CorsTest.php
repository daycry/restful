<?php

declare(strict_types=1);

namespace Tests\Filters;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\CorsFilter;
use CodeIgniter\Config\Services;

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

    public function testCorsSuccess(): void
    {
        $this->inkectMockAttributes(['checkCors' => true]);

        $request = service('request');
        $request->setHeader('Origin', 'https://test-cors1.local');
        
        Services::injectMock('request', $request);

        $result = $this->call('get', 'example-filter');

        $result->assertStatus(200);
        $result->assertSee("Passed");
        $result->assertHeader('Access-Control-Allow-Origin', '*');
    }

    public function testCorsError(): void
    {
        $this->inkectMockAttributes(['checkCors' => true, 'allowedOrigins' => $this->domains]);

        $request = service('request');
        $request->setHeader('Origin', 'https://test-cors1.local');
        
        Services::injectMock('request', $request);

        $result = $this->call('get', 'example-filter');

        $response = Services::response();
        
        $this->assertNotEquals(
            'https://test-cors1.local',
            $response->getHeaderLine('Access-Control-Allow-Origin')
        );

        //$result->assertHeaderMissing('Access-Control-Allow-Origin');
        $result->assertHeaderMissing('Access-Control-Allow-Credentials');
    }

    

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
