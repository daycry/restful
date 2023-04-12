<?php

declare(strict_types=1);

namespace Daycry\RestFul\Interfaces;

use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

interface LibraryAuthenticatorInterface
{
    public function __construct(UserModel $provider);
    public function check(string $username, ?string $password = null): ?User;
}
