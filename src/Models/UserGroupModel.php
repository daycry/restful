<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\UserGroup;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Entities\UserIdentity;

class UserGroupModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = UserGroup::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'user_id',
        'group_id'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['users_groups'];
    }

    /**
     * Returns all user identities.
     *
     * @return UserIdentity[]
     */
    public function getGroups(User $user): ?array
    {
        return $this->where('user_id', $user->id)->orderBy($this->primaryKey)->findAll();


    }
}
