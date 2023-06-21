<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Tests\Support\Authenticators\BasicAuthenticatorLibrary;
use Tests\Support\Authenticators\BasicBadAuthenticatorLibrary;

class LibraryTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAuthBasicLibrarySuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => ['basic' => 'library'], 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthBadBasicLibraryImplementation(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => ['basic' => 'library'], 'libraryCustomAuthenticators' => ['basic' => BasicBadAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidLibraryImplementation'), $content->messages->error);
    }

    public function testAuthInvalidAuthenticatorLibraryImplementation(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => ['basic' => 'library'] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.unknownAuthenticator', ['basic']), $content->messages->error);
    }

    public function testAuthBasicLibraryInvalidUsername(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => ['basic' => 'library'], 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry1:password')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthBasicLibraryInvalidPassword(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'basic', 'authSource' => ['basic' => 'library'], 'libraryCustomAuthenticators' => ['basic' => BasicAuthenticatorLibrary::class ] ]);

        $this->withHeaders([
            'Authorization' => 'Basic ' . \base64_encode('daycry:password1')
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
