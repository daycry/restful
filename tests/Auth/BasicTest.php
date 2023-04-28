<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class BasicTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

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
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthBasicWithoutUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode(':password')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthBasicWithoutPassword(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.noPassword'), $content->messages->error);
    }

    public function testAuthBasicIncorrectUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('d:p')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthBasicIncorrectPassword(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:p')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
