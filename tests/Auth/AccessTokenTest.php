<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class AccessTokenTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

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
            'X-API-KEY' => '456abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAccessToken'), $content->messages->error);
    }

    public function testAuthAccessTokenWithoutAccessToken(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token']);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAccessToken'), $content->messages->error);
    }

    public function testAuthAccessTokenWithInvalidIp(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'token']);

        $this->withHeaders([
            'X-API-KEY' => '123abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
