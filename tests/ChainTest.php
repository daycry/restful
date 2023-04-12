<?php

declare(strict_types=1);

namespace Tests;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use CodeIgniter\Config\Services;
use Daycry\RestFul\Filters\AjaxFilter;
use Daycry\RestFul\Filters\CorsFilter;
use Daycry\RestFul\Filters\AttemptFilter;
use Daycry\RestFul\Filters\BlackListFilter;
use Daycry\RestFul\Filters\WhiteListFilter;
use Daycry\RestFul\Filters\AuthFilter;
use Daycry\RestFul\Filters\AccessTokenFilter;
use Daycry\RestFul\Filters\AccessFilter;
use Daycry\RestFul\Filters\LimitFilter;
use Daycry\RestFul\Models\AttemptModel;

/**
 * @internal
 */
final class ChainTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'chain';
    protected mixed $classname = [
        AjaxFilter::class,
        CorsFilter::class,
        AttemptFilter::class,
        BlackListFilter::class,
        WhiteListFilter::class,
        AuthFilter::class,
        AccessTokenFilter::class,
        AccessFilter::class,
        LimitFilter::class
    ];

    public function testChainSuccess(): void
    {
        helper('auth');

        $this->inkectMockAttributes([
            'defaultAuth' => 'basic',
            'enableInvalidAttempts' => true,
            'ipBlacklistEnabled' => true,
            'ipWhitelistEnabled' => true,
            'ipWhitelist' => ['0.0.0.0'],
            'enableCheckAccess' => true,
            'accessTokenEnabled' => true,
            'enableLimit' => true,
            'requestLimit' => 15,
            'strictApiAndAuth' => true
        ]);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $attemtpModel = new AttemptModel();
        $attemtpModel->where('ip_address', (Services::request())->getIPAddress())->set(['attempts' => 1])->update();

        $this->withHeaders([
            'X-API-KEY' => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
