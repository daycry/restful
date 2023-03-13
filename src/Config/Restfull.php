<?php

declare(strict_types=1);

namespace Daycry\RestFull\Config;

use CodeIgniter\Config\BaseConfig;

class RestFull extends BaseConfig
{
    /**
     * --------------------------------------------------------------------
     * Customize Name of Tables
     * --------------------------------------------------------------------
     * Only change if you want to rename the default RestFull table names
     *
     * @var array<string, string>
     */
    public array $tables = [
        'users'                 => 'ws_users',
        'types'                 => 'ws_types',
        'identities'            => 'ws_users_identities',
        'permissions'           => 'ws_permissions',
        'groups'                => 'ws_groups',
        'identities_groups'     => 'ws_identities_groups',
        'identities_permissions'=> 'ws_identities_permissions',
        'groups_permissions'    => 'ws_groups_permissions',
        'logs'                  => 'ws_logs',
        'apis'                  => 'ws_apis',
        'namespaces'            => 'ws_namespaces',
        'endpoints'             => 'ws_endpoints',
        'attemps'               => 'ws_attemps',
        'limits'                => 'ws_limits'
    ];
}