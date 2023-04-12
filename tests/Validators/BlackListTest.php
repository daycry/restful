<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\BlackListFilter;

/**
 * @internal
 */
final class BlackListTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'blacklist';
    protected mixed $classname = BlackListFilter::class;

    public function testBlackListIpDenied(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.0']]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.ipDenied'), $result->response()->getReasonPhrase());
    }

    public function testBlackListRangeDenied(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.*']]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.ipDenied'), $result->response()->getReasonPhrase());

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0/24']]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.ipDenied'), $result->response()->getReasonPhrase());

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['0.0.0.0/255.255.255.0']]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.ipDenied'), $result->response()->getReasonPhrase());
    }

    public function testBlackListIpPassed(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['1.1.1.1']]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    public function testBlackListRangePassed(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1/24']]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1.1/255.255.255.0']]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipBlacklistEnabled' => true, 'ipBlacklist' => ['192.168.1.*']]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    public function testBlackListIpDisabled(): void
    {
        $this->inkectMockAttributes(['ipBlacklistEnabled' => false]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
