<?php

declare(strict_types=1);

namespace Tests\Support;

use CodeIgniter\Config\Factories;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Tests\Support\TestCase;

/**
 * @internal
 */
abstract class FilterTestCase extends TestCase
{
    use FeatureTestTrait;

    protected string $routeFilter;
    protected $namespace;
    protected string $alias;
    protected mixed $classname;

    protected function setUp(): void
    {
        Services::reset(true);

        parent::setUp();

        // Register our filter
        $this->registerFilter();

        // Add a test route that we can visit to trigger.
        $this->addRoutes();
    }

    private function registerFilter(): void
    {
        $filterConfig = config('Filters');

        $filterConfig->aliases[$this->alias] = $this->classname;

        Factories::injectMock('filters', 'filters', $filterConfig);
    }

    private function addRoutes(): void
    {
        $routes = service('routes');

        $filterString = ! empty($this->routeFilter)
            ? $this->routeFilter
            : $this->alias;

        $routes->group(
            '/',
            ['filter' => $filterString],
            static function ($routes): void {
                $routes->get('protected-route', static function (): void {
                    echo 'Protected';
                });
            }
        );

        $routes->options('filter-route', static function (): void {
            echo 'Passed';
        }, ['filter' => $this->alias . ':users-read']);

        $routes->get('filter-route', static function (): void {
            echo 'Passed';
        }, ['filter' => $this->alias . ':users-read']);

        $routes->get('example', '\Tests\Support\Controllers\Example::read', ['filter' => $this->alias . ':users.read']);
        $routes->get('example-write', '\Tests\Support\Controllers\Example::write', ['filter' => $this->alias . ':users.write']);
        $routes->get('example-noread', '\Tests\Support\Controllers\Example::noread', ['filter' => $this->alias . ':admins.noread']);

        Services::injectMock('routes', $routes);
    }
}
