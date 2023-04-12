<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Daycry\RestFul\Entities\Controller;
use Daycry\RestFul\Entities\Api;

class ControllerModel extends BaseModel
{
    protected $primaryKey     = 'id';
    protected $returnType     = Controller::class;
    protected $useSoftDeletes = false;

    protected $allowedFields  = [
        'api_id',
        'controller',
        'checked_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        parent::__construct($db, $validation);

        $this->table = $this->tables['controllers'];
    }

    /**
     * Returns all controllers.
     *
     * @return Controller[]
     */
    public function getControllers(Api $api): ?array
    {
        return $this->where('api_id', $api->id)->orderBy($this->primaryKey)->findAll();
    }
}
