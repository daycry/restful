<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

/**
 * @internal
 */
final class WhiteListTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testWhiteListIpSuccess(): void
    {
        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['0.0.0.0']]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testWhiteListRangePassed(): void
    {
        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['0.0.0.*']]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['0.0.0/24']]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");

        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['0.0.0.0/255.255.255.0']]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testWhiteListIpDenied(): void
    {
        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['1.1.1.1']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);
    }

    public function testWhiteListRangeDenied(): void
    {
        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['20.52.16/24']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);

        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipWhitelist' => ['20.52.16.4/255.255.255.0']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);

        $this->inkectMockAttributes(['ipWhitelistEnabled' => true, 'ipBlacklist' => ['20.52.16.*']]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(401);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Unauthorized', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ipDenied'), $content->messages->error);
    }

    public function testWhiteListIpDisabled(): void
    {
        $this->inkectMockAttributes(['ipWhitelistEnabled' => false]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
