<?php

namespace Tests\Auth;

use Tests\Support\TestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;

class DigestTest extends TestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testAuthDigestSuccess(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $this->withHeaders([
            'Authorization' => 'Digest username="daycry", nonce="762eaef6b22ea55c4ce7223148a23bdd", uri="/example", response="595ee1a396807388cb27d1af805017be", qop="auth", nc="00000002", cnonce="264e5043000b4bda"'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(200);
        $result->assertSee("Passed");
    }

    public function testAuthDigestWithoutCredentials(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthDigestWithoutUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $this->withHeaders([
            'Authorization' => 'Digest username="", nonce="762eaef6b22ea55c4ce7223148a23bdd", uri="/example", response="595ee1a396807388cb27d1af805017be", qop="auth", nc="00000002", cnonce="264e5043000b4bda"'
        ]);

        $result = $this->call('get', 'example');

        $content = \json_decode($result->getJson());

        $result->assertStatus(403);
        $this->assertTrue(isset($content->messages->error));
        $this->assertSame('Forbidden', $result->response()->getReasonPhrase());
        $this->assertSame(lang('RestFul.invalidCredentials'), $content->messages->error);
    }

    public function testAuthDigestIncorrectUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $this->withHeaders([
            'Authorization' => 'Digest username="daycry1", nonce="762eaef6b22ea55c4ce7223148a23bdd", uri="/example", response="595ee1a396807388cb27d1af805017be", qop="auth", nc="00000002", cnonce="264e5043000b4bda"'
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
