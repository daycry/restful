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
final class AccessTokenTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testAuthAccessTokenSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token']);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthAccessTokenFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token']);

        $this->withHeaders([
            'X-API-KEY' => '123abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidAccessToken'), $result->response()->getReasonPhrase());
    }

    public function testAuthAccessTokenWithoutAccessToken(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token']);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidAccessToken'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
