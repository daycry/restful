<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class BadAuthenticatorTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testBadAuthSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic1']);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.unknownAuthenticator', ['basic1']), $content->messages->error);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
