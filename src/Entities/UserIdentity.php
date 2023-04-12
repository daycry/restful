<?php

namespace Daycry\RestFul\Entities;

use CodeIgniter\Entity\Entity;
use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Traits\GetElementById;
use Daycry\RestFul\Libraries\Passwords;

class UserIdentity extends Entity
{
    use GetElementById;
    /**
     * User $user
     */
    private ?User $user = null;

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
        'expires'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'id'            => '?integer',
        'extra'        => 'serialize',
        'force_reset'   => 'intBol',
        'ignore_limits' => 'intBol',
        'is_private'    => 'intBol',
        'ip_addresses'  => 'serialize'
    ];

    protected $castHandlers = [
        'intBol' => \Daycry\RestFul\Entities\Cast\IntBoolCast::class,
        'serialize' => \Daycry\RestFul\Entities\Cast\SerializeCast::class
    ];

    /**
     * Uses password-strength hashing to hash
     * a given value for the 'secret'.
     */
    public function hashSecret(string $value): UserIdentity
    {
        /** @var Passwords $passwords */
        $passwords = service('passwords');

        $this->attributes['secret'] = $passwords->hash($value);

        return $this;
    }

    /**
     * Get User
     */
    public function getUser(): ?User
    {
        if ($this->user) {
            return $this->user;
        }

        $this->user = $this->getById(model(service('settings')->get('RestFul.userProvider')), $this->attributes['user_id']);

        return $this->user;
    }
}
