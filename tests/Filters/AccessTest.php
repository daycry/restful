<?php

declare(strict_types=1);

namespace Tests\Filters;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AccessFilter;
use CodeIgniter\Config\Services;

/**
 * @internal
 */
final class AccessTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'access';
    protected mixed $classname = AccessFilter::class;

    public function testAccessSuccess(): void
    {
        helper('auth');

        $this->inkectMockAttributes(['enableCheckAccess' => true, 'defaultAuth' => 'basic']);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $result = $this->call('get', 'example-filter');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAccessGroupSuccess(): void
    {
        helper('auth');

        $this->inkectMockAttributes(['enableCheckAccess' => true, 'defaultAuth' => 'basic']);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $result = $this->call('get', 'example-filter-write');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAccessNotEnoughPrivilege(): void
    {
        helper('auth');

        $this->inkectMockAttributes(['enableCheckAccess' => true, 'defaultAuth' => 'basic']);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));
        Services::injectMock('request', $request);

        auth('basic')->authenticate();

        $result = $this->call('get', 'example-filter-noread');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.notEnoughPrivilege'), $result->response()->getReasonPhrase());
    }

    public function testAccessWithoutAuthentication(): void
    {
        $this->inkectMockAttributes(['enableCheckAccess' => true]);

        $result = $this->call('get', 'example-filter');
        $result->assertStatus(401);
        $this->assertSame(lang('RestFul.notEnoughPrivilege'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
