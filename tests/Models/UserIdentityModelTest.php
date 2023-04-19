<?php

declare(strict_types=1);

namespace Tests\Models;

use Tests\Support\DatabaseTestCase;
use Tests\Support\Database\Seeds\TestSeeder;
use Daycry\RestFul\Exceptions\DatabaseException;
use Daycry\RestFul\Exceptions\LogicException;
use Daycry\RestFul\Models\UserIdentityModel;
use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\UserIdentity;
use Daycry\RestFul\Entities\User;

/**
 * @internal
 */
final class UserIdentityModelTest extends DatabaseTestCase
{
    protected $namespace = '\Daycry\RestFul';
    protected $seed = TestSeeder::class;

    private function _createUserIdentityModel(): UserIdentityModel
    {
        return new UserIdentityModel();
    }

    public function testCreateDuplicateRecordThrowsException(): void
    {
        $this->expectException(DatabaseException::class);

        $model = $this->_createUserIdentityModel();

        // "type and secret" are unique.
        $model->create([
            'user_id' => 1,
            'type'    => 'token',
            'secret'  => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593'
        ]);
    }

    public function testCheckUserWithoutId(): void
    {
        $this->expectException(LogicException::class);

        $user = new User();
        $user->username = 'fake';
        $user->scopes = ['users.read'];

        $model = $this->_createUserIdentityModel();
        $identity = $model->getIdentityByType($user, 'basic');

    }

    public function testGetAllIdentitiesByUser(): void
    {
        $user = (model(UserModel::class))->find(1);

        $model = $this->_createUserIdentityModel();
        $identities = $model->getIdentities($user);

        $this->assertCount(3, $identities);
    }

    public function testGetAllIdentitiesByUserIds(): void
    {
        $model = $this->_createUserIdentityModel();
        $identities = $model->getIdentitiesByUserIds([1]);

        $this->assertCount(3, $identities);
    }

    public function testGetIdentityByType(): void
    {
        $user = (model(UserModel::class))->find(1);

        $model = $this->_createUserIdentityModel();
        $identity = $model->getIdentityByType($user, 'basic');

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertEquals(1, $identity->id);

        $model->touchIdentity($identity);
    }

    public function testGetIdentityByTypes(): void
    {
        $user = (model(UserModel::class))->find(1);

        $model = $this->_createUserIdentityModel();
        $identities = $model->getIdentitiesByTypes($user, ['basic', 'token']);

        $this->assertCount(2, $identities);
    }

    public function testGetIdentityByTypesEmpty(): void
    {
        $user = (model(UserModel::class))->find(1);

        $model = $this->_createUserIdentityModel();
        $identities = $model->getIdentitiesByTypes($user, []);

        $this->assertCount(0, $identities);
    }

    public function testGetIdentityBySecret(): void
    {

        $model = $this->_createUserIdentityModel();
        $identity = $model->getIdentityBySecret('token', '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593');

        $this->assertInstanceOf(UserIdentity::class, $identity);
        $this->assertEquals(3, $identity->id);
    }

    public function testGetIdentityByNullSecret(): void
    {

        $model = $this->_createUserIdentityModel();
        $identity = $model->getIdentityBySecret('token', null);

        $this->assertEquals(null, $identity);
    }
}
