<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Exceptions\LogicException;
use Daycry\RestFul\Entities\UserIdentity;
use Daycry\RestFul\Entities\User;
use CodeIgniter\I18n\Time;

class UserIdentityModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = UserIdentity::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'user_id',
        'type',
        'secret',
        'refresh',
        'extra',
        'expires',
        'force_reset',
        'ignore_limits',
        'is_private',
        'ip_addresses',
        'last_used_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['identities'];
    }

    /**
     * Inserts a record
     *
     * @param array|object $data
     *
     * @throws DatabaseException
     */
    public function create($data): void
    {
        $this->disableDBDebug();

        $return = $this->insert($data);

        $this->checkQueryReturn($return);
    }

    /**
     * Returns all user identities.
     *
     * @return UserIdentity[]
     */
    public function getIdentities(User $user): ?array
    {
        return $this->where('user_id', $user->id)->orderBy($this->primaryKey)->findAll();
    }

    /**
     * @param int[]|string[] $userIds
     *
     * @return UserIdentity[]
     */
    public function getIdentitiesByUserIds(array $userIds): array
    {
        return $this->whereIn('user_id', $userIds)->orderBy($this->primaryKey)->findAll();
    }

    /**
     * Returns the first identity of the type.
     */
    public function getIdentityByType(User $user, string $type): ?UserIdentity
    {
        $this->checkUserId($user);

        return $this->where('user_id', $user->id)
            ->where('type', $type)
            ->orderBy($this->primaryKey)
            ->first();
    }

    /**
     * Returns all identities for the specific types.
     *
     * @param string[] $types
     *
     * @return UserIdentity[]
     */
    public function getIdentitiesByTypes(User $user, array $types): array
    {
        $this->checkUserId($user);

        if ($types === []) {
            return [];
        }

        return $this->where('user_id', $user->id)
            ->whereIn('type', $types)
            ->orderBy($this->primaryKey)
            ->findAll();
    }

    public function getIdentityBySecret(string $type, ?string $secret): ?UserIdentity
    {
        if ($secret === null) {
            return null;
        }

        return $this->where('type', $type)
            ->where('secret', $secret)
            ->first();
    }

    /**
     * Update the last used at date for an identity record.
     */
    public function touchIdentity(UserIdentity $identity): void
    {
        $identity->last_used_at = Time::now()->format('Y-m-d H:i:s');

        $return = $this->save($identity);

        $this->checkQueryReturn($return);
    }

    private function checkUserId(User $user): void
    {
        if ($user->id === null) {
            throw new LogicException(
                '"$user->id" is null. You should not use the incomplete User object.'
            );
        }
    }
}
