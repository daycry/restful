<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\Log;

class LogModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = Log::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'user_id',
        'uri',
        'method',
        'params',
        'ip_address',
        'duration',
        'response_code',
        'authorized'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['logs'];
    }
}
