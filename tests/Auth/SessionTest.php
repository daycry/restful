<?php

declare(strict_types=1);

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AuthFilter;

/**
 * @internal
 */
final class SessionTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAuthSessionSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => ['session' => 'sessionTest']]);

        $values = [
            'sessionTest' => 'daycry',
        ];

        $this->withSession($values);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthSessionFailed(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => ['session' => 'sessionTest']]);

        $values = [
            'sessionTest' => 'daycry1',
        ];

        $this->withSession($values);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthSessionWithoutSession(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => ['session' => 'sessionTest']]);

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
