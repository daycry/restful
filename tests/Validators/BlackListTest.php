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
final class BlackListTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testBlackListIpDenied(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.0']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);
    }

    public function testBlackListRangeDenied(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.*']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0/24']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.0/255.255.255.0']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);
    }

    public function testBlackListIpPassed(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['1.1.1.1']]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testBlackListRangePassed(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1/24']]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1.1/255.255.255.0']]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1.*']]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testBlackListIpDisabled(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => false]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
