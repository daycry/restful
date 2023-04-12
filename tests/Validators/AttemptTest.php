<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AttemptFilter;
use Daycry\RestFul\Models\AttemptModel;
use CodeIgniter\Config\Services;
use CodeIgniter\I18n\Time;

/**
 * @internal
 */
final class AttemptTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'attempt';
    protected mixed $classname = AttemptFilter::class;

    public function testAttemptError(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(429);
        $this->assertSame(lang('RestFul.invalidAttemptsLimit'), $result->response()->getReasonPhrase());
        $this->assertTrue($result->response()->hasHeader('X-RATE-LIMIT-RESET'));
    }

    public function testAttemptNoLimitSuccess(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $attemtpModel = new AttemptModel();
        $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->set(['attempts' => 1])->update();

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    public function testAttemptLimitSuccess(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $attemtpModel = new AttemptModel();
        $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->set(['hour_started_at' => Time::yesterday()])->update();

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
