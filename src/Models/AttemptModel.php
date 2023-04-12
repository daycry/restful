<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\Attempt;

class AttemptModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = Attempt::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'user_id',
        'ip_address',
        'attempts',
        'hour_started_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['attemps'];
    }
}
