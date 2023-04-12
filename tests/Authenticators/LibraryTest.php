<?php

declare(strict_types=1);

namespace Tests\Authenticators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AuthFilter;
use Tests\Support\Authenticators\BasicAuthenticatorLibrary;
use Tests\Support\Authenticators\BasicBadAuthenticatorLibrary;

/**
 * @internal
 */
final class LibraryTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

    public function testAuthBasicLibrarySuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => 'library', 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthBadBasicLibraryImplementation(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => 'library', 'libraryCustomAuthenticators' => ['basic' => BasicBadAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidLibraryImplementation'), $result->response()->getReasonPhrase());
    }

    public function testAuthInvalidAuthenticatorLibraryImplementation(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => 'library' ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.unknownAuthenticator', ['basic']), $result->response()->getReasonPhrase());
    }

    public function testAuthBasicLibraryInvalidUsername(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => 'library', 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry1:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthBasicLibraryInvalidPassword(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => 'library', 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password1')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
