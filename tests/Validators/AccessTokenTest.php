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
final class AccessTokenTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAccessTokenError(): void
    {
        $this->inkectMockAttributes(['accessTokenEnabled' => true]);

        $this->withHeaders([
            'X-API-KEY' => 'g7fabf7e82c4a94d494895da0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAccessToken'), $content->messages->error);
    }

    public function testAccessTokenSuccess(): void
    {
        $this->inkectMockAttributes(['accessTokenEnabled' => true]);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testAccessTokenWithoutToken(): void
    {
        $this->inkectMockAttributes(['accessTokenEnabled' => true]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAccessToken'), $content->messages->error);
    }

    public function testAccessTokenApiAndAuthStrictFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'accessTokenEnabled' => true, 'strictApiAndAuth' => true]);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593',
            'Authorization' => 'Basic ' . \base64_encode('daycry:password2')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAccessTokenApiAndAuthStrictSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'accessTokenEnabled' => true, 'strictApiAndAuth' => true]);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593',
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testAccessTokenApiAndAuthNoStrictSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'accessTokenEnabled' => true, 'strictApiAndAuth' => false]);

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593',
            'Authorization' => 'Basic ' . \base64_encode('daycry:password2')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
