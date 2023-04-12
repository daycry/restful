<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;
use Daycry\RestFul\Models\GroupModel;

class UserGroup extends Entity
{
    /**
     * User $user
     */
    private ?User $user = null;

    /**
     * Group $group
     */
    private ?Group $group = null;

    /**
     * @var string[]
     * @phpstan-var list<string>
     * @psalm-var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Get User
     */
    public function getUser()
    {
        if ($this->user) {
            return $this->user;
        }

        $userProvider = model(service('settings')->get('RestFul.userProvider'));
        $this->user = $userProvider->where('id', $this->attributes['user_id'])->first();

        return $this->user;
    }

    /**
     * Get Group
     */
    public function getGroup()
    {
        if ($this->group) {
            return $this->group;
        }

        $groupModel = model(GroupModel::class);
        $this->group = $groupModel->where('id', $this->attributes['group_id'])->first();

        return $this->group;
    }
}
