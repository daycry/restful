<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AjaxFilter;

/**
 * @internal
 */
final class AjaxTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'ajax';
    protected mixed $classname = AjaxFilter::class;

    public function testAjaxError(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => true]);

        $result = $this->call('get', 'filter-route');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.ajaxOnly'), $result->response()->getReasonPhrase());
    }

    public function testAjaxXMLHttpRequestSuccess(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => true]);

        $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    public function testAjaxSuccess(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => false]);

        $result = $this->call('get', 'filter-route');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
