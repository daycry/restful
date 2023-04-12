<?php

declare(strict_types=1);

namespace Tests\Authenticators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AuthFilter;
use Daycry\JWT\JWT;

/**
 * @internal
 */
final class BearerTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testAuthBearerSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'bearer']);

        $bearer = (new JWT())->encode(['username' => 'daycry', 'text' => 'Content'], 'daycry');

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $bearer
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthBearerFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'bearer']);

        $bearer = (new JWT())->encode(['username' => 'daycry', 'text' => 'Content'], 'daycry2');

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $bearer
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthBearerWithoutBearer(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'bearer']);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
