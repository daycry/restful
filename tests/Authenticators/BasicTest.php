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
final class BasicTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testAuthBasicSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthBasicWithoutCredentials(): void
    {
        $request = service('request');

        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $result = $this->call('get', 'example');

        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthBasicWithoutUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode(':password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthBasicWithoutPassword(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.noPassword'), $result->response()->getReasonPhrase());
    }

    public function testAuthBasicIncorrectUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:p')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
