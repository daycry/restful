<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class WhiteListTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAuthWhitelistSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'whitelist', 'ipWhitelist' => ['0.0.0.0']]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthWhitelistFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'whitelist', 'ipWhitelist' => ['1.0.0.0']]);

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
