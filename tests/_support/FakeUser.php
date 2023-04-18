<?php

declare(strict_types=1);

namespace Tests\Support;

use Daycry\RestFul\Entities\User;
use Daycry\RestFul\Models\UserModel;

trait FakeUser
{
    private User $user;

    protected function setUpFakeUser(): void
    {
        $this->user = fake(UserModel::class);
    }
}
