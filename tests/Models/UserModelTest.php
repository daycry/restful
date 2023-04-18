<?php

declare(strict_types=1);

namespace Tests\Models;

use Tests\Support\DatabaseTestCase;
use Tests\Support\Database\Seeds\TestSeeder;
use Tests\Support\FakeUser;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Entities\UserIdentity;

/**
 * @internal
 */
final class UserModelTest extends DatabaseTestCase
{
    use FakeUser;

    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    public function testGetIdentitiesNone(): void
    {
        // when none, returns empty array
        $this->assertEmpty($this->user->identities);
    }

    public function testGetIdentitiesSome(): void
    {
        $this->user = (model(UserModel::class))->find(1);
        $this->assertCount(3, $this->user->identities);
    }

    public function testGetIdentitiesByType(): void
    {
        /** @var User $user */
        $user = (model(UserModel::class))->find(1);

        $identities = $user->getIdentities('token');

        $this->assertCount(1, $identities);
        $this->assertInstanceOf(UserIdentity::class, $identities[0]);
        $this->assertSame('token', $identities[0]->type);
        $this->assertEmpty($this->user->getIdentities('foo'));
    }

    public function testModelFindAllWithIdentities(): void
    {
        // Grab the user again, using the model's identity helper
        $this->user = (model(UserModel::class))->withIdentities()->find(1);

        $identities = $this->user->identities;

        $this->assertCount(3, $identities);
    }

    public function testModelFindAllWithIdentitiesUserNotExists(): void
    {
        $users = model(UserModel::class)->withIdentities()->onlyDeleted()->findAll();

        $this->assertSame([], $users);
    }
}
