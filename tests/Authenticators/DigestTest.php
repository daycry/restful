<?php

declare(strict_types=1);

namespace Tests\Authenticators;

use Tests\Support\FilterTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use CodeIgniter\Test\FeatureTestTrait;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Filters\AuthFilter;

/**
 * @internal
 */
final class DigestTest extends FilterTestCase
{
    use DatabaseTestTrait;
    use FeatureTestTrait;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    protected string $alias     = 'auth';
    protected mixed $classname = AuthFilter::class;

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
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthDigestWithoutUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $this->withHeaders([
            'Authorization' => 'Digest username="", nonce="762eaef6b22ea55c4ce7223148a23bdd", uri="/example", response="595ee1a396807388cb27d1af805017be", qop="auth", nc="00000002", cnonce="264e5043000b4bda"'
        ]);

        $result = $this->call('get', 'example');
        $result->assertStatus(403);
        $this->assertSame(lang('RestFul.invalidCredentials'), $result->response()->getReasonPhrase());
    }

    public function testAuthDigestIncorrectUser(): void
    {
        $this->inkectMockAttributes(['defaultAuth' => 'digest']);

        $this->withHeaders([
            'Authorization' => 'Digest username="daycry1", nonce="762eaef6b22ea55c4ce7223148a23bdd", uri="/example", response="595ee1a396807388cb27d1af805017be", qop="auth", nc="00000002", cnonce="264e5043000b4bda"'
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
