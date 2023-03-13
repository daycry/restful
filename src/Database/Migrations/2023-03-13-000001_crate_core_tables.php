<?php

declare(strict_types=1);

namespace CodeIgniter\Shield\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\Forge;
use CodeIgniter\Database\RawSql;

class CreateCoreTables extends Migration
{
    /**
     * Table names
     */
    private array $tables;

    /**
     * Access Token lenght
     */
    private int $accessTokenLength = 40;

    public function __construct(?Forge $forge = null)
    {
        parent::__construct($forge);

        /** @var RestFul $config */
        $config   = config('RestFul');
        $this->tables = $config->tables;
    }

    public function up(): void
    {
        // Users Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'username'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'active'         => ['type' => 'tinyint', 'constraint' => 1, 'null' => false, 'default' => 0],
            'last_active'    => ['type' => 'datetime', 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('username');
        $this->forge->createTable($this->tables['users']);

        // Types Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable($this->tables['types']);

        /*
         * Auth Identities Table
         * Used for storage of passwords, access tokens.
         */
        $this->forge->addField([
            'id'           => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'type_id'      => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'secret'       => ['type' => 'varchar', 'constraint' => 255],
            'expires'      => ['type' => 'datetime', 'null' => true],
            'force_reset'  => ['type' => 'tinyint', 'constraint' => 1, 'default' => 0],
            'last_used_at' => ['type' => 'datetime', 'null' => true],
            'created_at'   => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'   => ['type' => 'datetime', 'null' => true],
            'deleted_at'   => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['type_id', 'secret']);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', $this->tables['users'], 'id', '', 'CASCADE');
        $this->forge->addForeignKey('type_id', $this->tables['types'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['identities']);

        // Permissions Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable($this->tables['permissions']);

        // Groups Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name'       => ['type' => 'varchar', 'constraint' => 30, 'null' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('name');
        $this->forge->createTable($this->tables['groups']);

        // Users Groups Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'identity_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'group_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['identity_id','group_id']);
        $this->forge->addForeignKey('identity_id', $this->tables['identities'], 'id', '', 'CASCADE');
        $this->forge->addForeignKey('group_id', $this->tables['groups'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['identities_groups']);

        // Identities Permissions Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'identity_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'permission_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['identity_id','permission_id']);
        $this->forge->addForeignKey('identity_id', $this->tables['identities'], 'id', '', 'CASCADE');
        $this->forge->addForeignKey('permission_id', $this->tables['permissions'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['identities_permissions']);

        // Groups Permissions Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'group_id'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'permission_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true],
            'deleted_at'     => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['group_id','permission_id']);
        $this->forge->addForeignKey('group_id', $this->tables['groups'], 'id', '', 'CASCADE');
        $this->forge->addForeignKey('permission_id', $this->tables['permissions'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['groups_permissions']);

        // Logs Table
        $this->forge->addField([
            'id'                    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'identity_id'           => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'uri'                   => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'method'                => ['type' => 'varchar', 'constraint' => 6, 'null' => false],
            'params'                => ['type' => 'text', 'null' => true, 'default' => null],
            'ip_address'            => ['type' => 'varchar', 'constraint' => 45, 'null' => false],
            'duration'              => ['type' => 'float', 'null' => true, 'default' => null],
            'authorized'            => ['type' => 'tinyint', 'constraint' => 1, 'null' => false],
            'response_code'         => ['type' => 'int', 'constraint' => 3, 'null' => true],
            'created_at'            => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'            => ['type' => 'datetime', 'null' => true, 'default' => null ],
            'deleted_at'            => ['type' => 'datetime', 'null' => true, 'default' => null]
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('identity_id', $this->tables['identities'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['logs']);

        // Apis Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'url'            => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'checked_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP') ],
            'created_at'     => ['type' => 'datetime', 'null' => true, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true, 'default' => null ],
            'deleted_at'     => ['type' => 'datetime', 'null' => true, 'default' => null]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('checked_at');
        $this->forge->addUniqueKey('url');
        $this->forge->createTable($this->tables['apis']);

        //namespaces Table
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'api_id'        => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'controller'    => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'checked_at'    => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP') ],
            'created_at'    => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'    => ['type' => 'datetime', 'null' => true, 'default' => null ],
            'deleted_at'    => ['type' => 'datetime', 'null' => true, 'default' => null]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('checked_at');
        $this->forge->addKey('controller', false, true);
        $this->forge->addForeignKey('api_id', $this->tables['apis'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['namespaces']);

        //endpoints Table
        $this->forge->addField([
            'id'            => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'namespace_id'  => ['type' => 'int', 'constraint' => 11, 'unsigned' => true],
            'method'        => ['type' => 'varchar', 'constraint' => 255, 'null' => false],
            'checked_at'    => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'http'          => ['type' => 'varchar', 'constraint' => 10, 'null' => true, 'default' => null],
            'auth'          => ['type' => 'varchar', 'constraint' => 10, 'null' => true, 'default' => null],
            'acces_token'   => ['type' => 'tinyint', 'constraint' => 1, 'null' => true, 'default' => null],
            'log'           => ['type' => 'tinyint', 'constraint' => 1, 'null' => true, 'default' => null],
            'limit'         => ['type' => 'tinyint', 'constraint' => 1, 'null' => true, 'default' => null],
            'time'          => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'default' => null],
            'scope'         => ['type' => 'int', 'constraint' => 11, 'null' => true, 'default' => null],
            'created_at'    => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'    => ['type' => 'datetime', 'null' => true, 'default' => null],
            'deleted_at'    => ['type' => 'datetime', 'null' => true, 'default' => null]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('checked_at');
        $this->forge->addForeignKey('namespace_id', $this->tables['namespaces'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['endpoints']);

        //attemps Table
        $this->forge->addField([
            'id'             => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'identity_id'    => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => true, 'default' => null],
            'ip_address'     => ['type' => 'varchar', 'constraint' => 45, 'null' => false],
            'attempts'       => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'null' => false, 'default' => 0],
            'hour_started_at'=> ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'created_at'     => ['type' => 'datetime', 'null' => false, 'default' => new RawSql('CURRENT_TIMESTAMP')],
            'updated_at'     => ['type' => 'datetime', 'null' => true, 'default' => null ],
            'deleted_at'     => ['type' => 'datetime', 'null' => true, 'default' => null]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['identity_id','ip_address']);
        $this->forge->addForeignKey('identity_id', $this->tables['identities'], 'id', '', 'CASCADE');
        $this->forge->createTable($this->tables['endpoints']);
    }

    public function down(): void
    {
        $this->db->disableForeignKeyChecks();

        $this->forge->dropTable($this->tables['users'], true);
        $this->forge->dropTable($this->tables['permissions'], true);
        $this->forge->dropTable($this->tables['groups'], true);
        $this->forge->dropTable($this->tables['identities_groups'], true);
        $this->forge->dropTable($this->tables['users_permissions'], true);
        $this->forge->dropTable($this->tables['groups_permissions'], true);
        $this->forge->dropTable($this->tables['tokens'], true);
        $this->forge->dropTable($this->tables['logs'], true);
        $this->forge->dropTable($this->tables['apis'], true);
        $this->forge->dropTable($this->tables['namespaces'], true);
        $this->forge->dropTable($this->tables['endpoints'], true);
        $this->forge->dropTable($this->tables['attemps'], true);

        $this->db->enableForeignKeyChecks();
    }
}