<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;
use Daycry\RestFul\Models\UserIdentityModel;
use Daycry\RestFul\Models\UserGroupModel;
use Daycry\RestFul\Models\GroupModel;
use Daycry\RestFul\Traits\Authorizable;

class User extends Entity
{
    use Authorizable;
    /**
     * @var UserIdentity[]|null
     */
    private ?array $identities = null;

    /**
     * @var Group[]|null
     */
    private ?array $groups = null;

    /**
     * @var string[]
     * @phpstan-var list<string>
     * @psalm-var list<string>
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'last_active',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'           => '?integer',
        'scopes'       => 'serialize',
        'active'       => '?intBol'
    ];

    protected $castHandlers = [
        'intBol' => \Daycry\RestFul\Entities\Cast\IntBoolCast::class,
        'serialize' => \Daycry\RestFul\Entities\Cast\SerializeCast::class
    ];

    /**
     * Returns the first identity of the given $type for this user.
     *
     * @param string $type See const ID_TYPE_* in Authenticator.
     *                     'email_2fa'|'email_activate'|'email_password'|'magic-link'|'access_token'
     */
    public function getIdentity(string $type): ?UserIdentity
    {
        $identities = $this->getIdentities($type);

        return count($identities) ? array_shift($identities) : null;
    }

    /**
     * Accessor method for this user's UserIdentity objects.
     * Will populate if they don't exist.
     *
     * @param string $type 'all' returns all identities.
     *
     * @return UserIdentity[]
     */
    public function getIdentities(string $type = 'all'): array
    {
        $this->populateIdentities();

        if ($type === 'all') {
            return $this->identities;
        }

        $identities = [];

        foreach ($this->identities as $identity) {
            if ($identity->type === $type) {
                $identities[] = $identity;
            }
        }

        return $identities;
    }

    /**
     * ensures that all of the user's identities are loaded
     * into the instance for faster access later.
     */
    private function populateIdentities(): void
    {
        if ($this->identities === null) {
            /** @var UserIdentityModel $identityModel */
            $identityModel = model(UserIdentityModel::class);
            $this->identities = $identityModel->getIdentities($this);
        }
    }

    /**
     * Get Groups
     */
    public function getGroups(string $name = 'all')
    {
        $this->populateGroups();

        $groups = [];
        if ($name === 'all') {
            return $this->groups;
        }

        foreach ($this->groups as $group) {
            if ($group->name === $name) {
                $groups[] = $group;
            }
        }

        return $groups;
    }
    /**
     * ensures that all of the user's groups are loaded
     * into the instance for faster access later.
     */
    private function populateGroups(): void
    {
        if ($this->groups === null) {
            /** @var UserGroupModel $userGroupModel */
            $userGroupModel = model(UserGroupModel::class);
            $userGroups = $userGroupModel->getGroups($this);

            $ids = [];
            foreach($userGroups as $userGroup) {
                $ids[] = $userGroup->group_id;
            }

            $groupModel = model(GroupModel::class);
            $this->groups = $groupModel->getGroupsByIds($ids);
        }
    }
}
