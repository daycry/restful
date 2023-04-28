<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class NoAuthenticatorTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testNoAuthSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => null]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
