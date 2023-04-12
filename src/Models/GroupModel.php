<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\Group;
use Daycry\RestFul\Entities\User;

class GroupModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = Group::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'name',
        'scopes'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['groups'];
    }

    /**
     * @param int[]|string[] $groupIds
     *
     * @return Group[]
     */
    public function getGroupsByIds(array $groupIds): array
    {
        return $this->whereIn('id', $groupIds)->orderBy($this->primaryKey)->findAll();
    }
}
