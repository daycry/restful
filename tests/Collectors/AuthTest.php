<?php

declare(strict_types=1);

namespace Tests\Collectors;

use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Auth as RestFulAuth;
use Daycry\RestFul\Authenticators\Basic;
use Daycry\RestFul\Collectors\Auth;

use Tests\Support\TestCase;

/**
 * @internal
 */
final class AuthTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;


    protected $namespace = '\Daycry\RestFul';
    protected $refresh = true;
    protected $seed = TestSeeder::class;
    private Auth $collector;

    protected function setUp(): void
    {
        parent::setUp();

        $this->collector = new Auth();
    }

    public function testDisplayNotLoggedIn(): void
    {
        $output = $this->collector->display();

        $this->assertStringContainsString('Not logged in', $output);
    }

    public function testDisplayLoggedIn(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        helper('auth');

        assert(auth()->getAuthenticator() instanceof Basic);

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));

        auth()->authenticate();

        $output = $this->collector->display();

        $this->assertStringContainsString('Current Use', $output);
        $this->assertStringContainsString('<td>Username</td><td>daycry</td>', $output);
        $this->assertStringContainsString('<td>Groups</td><td>admin</td>', $output);
        $this->assertStringContainsString('<td>Permissions</td><td>users.read</td>', $output);
    }

    public function testGetTitleDetails(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        $output = $this->collector->getTitleDetails();

        $this->assertStringContainsString(RestFulAuth::RESTFUL_VERSION, $output);
        $this->assertStringContainsString(Basic::class, $output);
    }

    public function testGetBadgeValueReturnsUserId(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic']);

        helper('auth');

        $request = service('request');
        $request->setHeader('Authorization', 'Basic ' . \base64_encode('daycry:password'));

        auth()->authenticate();

        $output = (string) $this->collector->getBadgeValue();

        $this->assertStringContainsString('1', $output);
    }
}
