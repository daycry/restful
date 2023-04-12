<?php

declare(strict_types=1);

namespace Daycry\RestFul\Interfaces;

use Daycry\RestFul\Models\UserModel;
use Daycry\RestFul\Entities\User;

interface AuthenticatorInterface
{
    public function __construct(UserModel $provider);
    public function check(): ?User;
    public function loggedIn(): bool;
    public function getUser(): ?User;
}
