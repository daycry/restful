<?php

declare(strict_types=1);

namespace Daycry\RestFul\Models;

use CodeIgniter\Model;
use CodeIgniter\Validation\ValidationInterface;
use CodeIgniter\Database\ConnectionInterface;
use Config\Database;
use Daycry\RestFul\Traits\CheckQueryReturnTrait;

abstract class BaseModel extends Model
{
    use CheckQueryReturnTrait;

    /**
     * Table names
     */
    protected array $tables;

    public function __construct(?ConnectionInterface &$db = null, ?ValidationInterface $validation = null)
    {
        if ($db === null) {
            $db = Database::connect(config('RestFul')->databaseGroup);
            $this->DBGroup = config('RestFul')->databaseGroup;
        }

        /** @var \Daycry\RestFul\Config\RestFul $config */
        $config = config('RestFul');

        $this->tables = $config->tables;

        parent::__construct($db, $validation);
    }
}
