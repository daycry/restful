<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\LimitFilter;
use Daycry\RestFul\Models\AttemptModel;
use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;

/**
 * @internal
 */
final class LimitTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'limit';
    protected mixed $classname = LimitFilter::class;

    public function testLimitNoAuthError(): void
    {
        $this->inkectMockAttributes(['enableLimit' => true]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.unknownAuthenticator', [null]), $result->response()->getReasonPhrase());
    }

    public function testLimitAuthError(): void
    {
        helper('auth');

        $this->inkectMockAttributes(['enableLimit' => true, 'defaultAuth' => 'basic', 'limitMethod' => 'USER']);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $result = $this->call('get', 'example');
        $result->assertStatus(429);
        $this->assertSame(lang('RestFul.invalidAttemptsLimit'), $result->response()->getReasonPhrase());
        $this->assertTrue($result->response()->hasHeader('X-RATE-LIMIT-RESET'));
    }

    public function testLimitSuccess(): void
    {
        helper('auth');

        $this->inkectMockAttributes(['enableLimit' => true, 'defaultAuth' => 'basic', 'limitMethod' => 'USER']);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $limitModel = new \Daycry\RestFul\Models\LimitModel();
        $limitModel->where('uri', 'user:daycry')->delete();

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
