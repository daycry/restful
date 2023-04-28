<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Config\Factories;
use Daycry\Settings\Settings;
use CodeIgniter\Test\CIUnitTestCase;
use Config\Services;

/**
 * @internal
 */
abstract class TestCase extends CIUnitTestCase
{
    protected function setUp(): void
    {
        $this->resetServices();

        parent::setUp();

        $this->addRoutes();

        // Use Array Settings Handler
        $configSettings           = config('Settings');
        $configSettings->handlers = ['array'];
        $settings                 = new Settings($configSettings);
        Services::injectMock('settings', $settings);
    }

    protected function inkectMockAttributes(array $attributes = [])
    {
        $config = config('RestFul');

        foreach ($attributes as $attribute => $value) {
            $config->{$attribute} = $value;
        }

        Factories::injectMock('config', 'RestFul', $config);
    }

    protected function addRoutes(): void
    {
        $routes = service('routes');

        $routes->get('example', '\Tests\Support\Controllers\Example::read');
        $routes->options('example', '\Tests\Support\Controllers\Example::read');

        Services::injectMock('routes', $routes);
    }
}
