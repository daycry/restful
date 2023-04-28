<?php

declare(strict_types=1);

namespace Tests\Validators;

use Tests\Support\TestCase;
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
final class LimitTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testLimit(): void
    {
        $this->inkectMockAttributes(['enableLimit' => true, 'requestLimit' => 1]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(200);
        $result->assertSee("Passed");

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(429);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Too Many Requests', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidAttemptsLimit'), $content->messages->error);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
