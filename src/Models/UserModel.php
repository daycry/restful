<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Entities\UserIdentity;

class UserModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = User::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'username',
        'scopes',
        'active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $afterFind     = ['fetchIdentities'];

    /**
     * Whether identity records should be included
     * when user records are fetched from the database.
     */
    protected bool $fetchIdentities = false;

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['users'];
    }

    /**
     * Mark the next find* query to include identities
     *
     * @return $this
     */
    public function withIdentities(): self
    {
        $this->fetchIdentities = true;

        return $this;
    }

    /**
     * Populates identities for all records
     * returned from a find* method. Called
     * automatically when $this->fetchIdentities == true
     *
     * Model event callback called by `afterFind`.
     */
    protected function fetchIdentities(array $data): array
    {
        if (! $this->fetchIdentities) {
            return $data;
        }

        $userIds = $data['singleton']
            ? array_column($data, 'id')
            : array_column($data['data'], 'id');

        if ($userIds === []) {
            return $data;
        }

        /** @var UserIdentityModel $identityModel */
        $identityModel = model(UserIdentityModel::class);

        // Get our identities for all users
        $identities = $identityModel->getIdentitiesByUserIds($userIds);

        if (empty($identities)) {
            return $data;
        }

        $mappedUsers = $this->assignIdentities($data, $identities);

        $data['data'] = $data['singleton'] ? $mappedUsers[array_column($data, 'id')[0]] : $mappedUsers;

        return $data;
    }

    /**
     * Map our users by ID to make assigning simpler
     *
     * @param array          $data       Event $data
     * @param UserIdentity[] $identities
     *
     * @return User[] UserId => User object
     * @phpstan-return array<int|string, User> UserId => User object
     */
    private function assignIdentities(array $data, array $identities): array
    {
        $mappedUsers = [];

        $users = $data['singleton'] ? [$data['data']] : $data['data'];

        foreach ($users as $user) {
            $mappedUsers[$user->id] = $user;
        }
        unset($users);

        // Now assign the identities to the user
        foreach ($identities as $identity) {
            $userId = $identity->user_id;

            $newIdentities   = $mappedUsers[$userId]->identities;
            $newIdentities[] = $identity;

            $mappedUsers[$userId]->identities = $newIdentities;
        }

        return $mappedUsers;
    }
}
