<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Models\AttemptModel;
use CodeIgniter\I18n\Time;

/**
 * @internal
 */
final class AttemptTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAttemptError(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(429);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Too Many Requests', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAttemptsLimit'), $content->messages->error);
        $this->assertTrue($result->response()->hasHeader('X-RATE-LIMIT-RESET'));
    }

    public function testAttemptNoLimitSuccess(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $attemtpModel = new AttemptModel();
        $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->set(['attempts' => 1])->update();

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testAttemptNoLimitFailed(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true, 'accessTokenEnabled' => true]);

        $attemtpModel = new AttemptModel();
        $attempt = $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->first();

        $attemtpModel->delete($attempt->id);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAccessToken'), $content->messages->error);
        $this->assertFalse($result->response()->hasHeader('X-RATE-LIMIT-RESET'));
    }

    public function testAttemptLimitSuccess(): void
    {
        $this->inkectMockAttributes(['enableInvalidAttempts' => true]);

        $attemtpModel = new AttemptModel();
        $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->set(['hour_started_at' => Time::yesterday()])->update();

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
