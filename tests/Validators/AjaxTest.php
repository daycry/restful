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
final class AjaxTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAjaxError(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => true]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.ajaxOnly'), $content->messages->error);
    }

    public function testAjaxXMLHttpRequestSuccess(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => true]);

        $this->withHeaders([
            'X-Requested-With' => 'XMLHttpRequest'
        ]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    public function testNoAjaxSuccess(): void
    {
        $this->inkectMockAttributes(['ajaxOnly' => false]);

        $result = $this->call('get', 'example');
        $result->assertSee("Passed");
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
