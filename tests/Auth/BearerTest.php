<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\JWT\JWT;

class BearerTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

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

        $bearer = (new JWT())->encode(['username' => 'daycry', 'text' => 'Content'], 'daycry3');

        $this->withHeaders([
            'Authorization' => 'Bearer ' . $bearer
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthBearerWithoutBearer(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'bearer']);

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
