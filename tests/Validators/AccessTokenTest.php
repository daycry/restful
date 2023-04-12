<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AccessTokenFilter;

/**
 * @internal
 */
final class AccessTokenTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'token';
    protected mixed $classname = AccessTokenFilter::class;

    public function testAccessTokenError(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token', 'accessTokenEnabled' => true]);

        $this->withHeaders([
            'X-API-KEY' => '123abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidAccessToken'), $result->response()->getReasonPhrase());
    }

    public function testAccessTokenSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token', 'accessTokenEnabled' => true]);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testAccessTokenWithoutToken(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token', 'accessTokenEnabled' => true]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidAccessToken'), $result->response()->getReasonPhrase());
    }

    public function testAccessTokenApiAndAuthFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'accessTokenEnabled' => true, 'strictApiAndAuth' => true]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }


    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
