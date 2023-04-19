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

        // Use Array Settings Handler
        $configSettings           = config('Settings');
        $configSettings->handlers = ['array'];
        $settings                 = new Settings($configSettings);
        Services::injectMock('settings', $settings);

        // Ensure from email is available anywhere during Tests
        helper('setting');
        setting('Email.fromEmail', 'foo@example.com');
        setting('Email.fromName', 'John Smith');

        // Set Config\Security::$csrfProtection to 'session'
        $config                 = config('Security');
        $config->csrfProtection = 'session';
        Factories::injectMock('config', 'Security', $config);
    }

    protected function inkectMockAttributes(array $attributes = [])
    {
        $config = config('RestFul');

        foreach ($attributes as $attribute => $value) {
            $config->{$attribute} = $value;
        }

        Factories::injectMock('config', 'RestFul', $config);
    }
}
