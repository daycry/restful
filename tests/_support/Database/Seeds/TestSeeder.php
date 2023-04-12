<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class TestSeeder extends Seeder
{
    public function run()
    {
        helper('text');

        $config   = config('RestFul');
        $tables = $config->tables;

        $token = hash('sha256', $rawToken = random_string('crypto', 64));

        $users = [
            [
                'username'=> 'daycry',
                'scopes'        => \serialize(['users.read']),
                'active'  => 1
            ]
        ];

        $this->db->table($tables['users'])->insertBatch($users);

        $identities = [
            [
                'user_id'       => 1,
                'type'       => 'basic',
                'secret'        => password_hash('password', PASSWORD_DEFAULT, ['cost'=>10]),
                'ignore_limits' => 0,
                'is_private'    => 0,
                'ip_addresses'  => \serialize(['0.0.0.0'])
            ],
            [
                'user_id'       => 1,
                'type'       => 'digest',
                'secret'        => md5('daycry:WEB SERVICE:password'),
                'ignore_limits' => 0,
                'is_private'    => 0,
                'ip_addresses'  => \serialize(['0.0.0.0'])
            ],
            [
                'user_id'       => 1,
                'type'       => 'token',
                'secret'        => '887abf7e82c4a94d4945bbda0958ee58ffe9117f9b317d586fc4192680033593',
                'ignore_limits' => 0,
                'is_private'    => 1,
                'ip_addresses'  => \serialize(['0.0.0.0'])
            ]
        ];

        $this->db->table($tables['identities'])->insertBatch($identities);

        $groups = [
            [
                'name'=> 'admin',
                'scopes'=> "a:1:{i:0;s:7:\"users.*\";}"
            ]
        ];

        $this->db->table($tables['groups'])->insertBatch($groups);

        $users_groups = [
            [
                'user_id' => 1,
                'group_id' => 1
            ]
        ];

        $this->db->table($tables['users_groups'])->insertBatch($users_groups);

        $apis = [
            [
                'url'=> site_url()
            ]
        ];

        // Using Query Builder
        $this->db->table($tables['apis'])->insertBatch($apis);

        $controllers = [
            [
                'controller'=> '\Tests\Support\Controllers\Example',
                'api_id'    => 1,
            ]
        ];

        // Using Query Builder
        $this->db->table($tables['controllers'])->insertBatch($controllers);

        $endpoints = [
            [
                'controller_id'  => 1,
                'method'        => 'read',
                'auth'          => null,
                'access_token'  => null,
                'log'           => null,
                'limit'         => 10,
                'time'          => 3600,
                'scope'        => "users.read"
            ]
        ];

        // Using Query Builder
        $this->db->table($tables['endpoints'])->insertBatch($endpoints);


        $limits = [
            [
                'user_id'    => 1,
                'uri'  => 'user:daycry',
                'count'                 => '10',
                'hour_started_at'        => new Time('now')
            ]
        ];

        // Using Query Builder
        $this->db->table($tables['limits'])->insertBatch($limits);

        $attempt = [
            'user_id'        => 1,
            'ip_address'     => '0.0.0.0',
            'attempts'       => '1000',
            'hour_started_at'    => new Time('now')
        ];

        // Using Query Builder
        $this->db->table($tables['attemps'])->insert($attempt);
    }
}
