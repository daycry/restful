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
final class SessionTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testAuthSessionSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => 'sessionTest']);

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
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => 'sessionTest']);

        $values = [
            'sessionTest' => 'daycry1',
        ];

        $this->withSession($values);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthSessionWithoutSession(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'session', 'authSource' => 'sessionTest']);

        $values = [
            'sessionTest1' => 'daycry1',
        ];

        $this->withSession($values);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
