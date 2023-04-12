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
final class BadAuthenticatorTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testBadAuthSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic1']);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.unknownAuthenticator', ['basic1']), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
