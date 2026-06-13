<?php

namespace App\Policies;

use App\Models\SalesClient;
use App\Models\User;

class SalesClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SalesClient $salesClient): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SalesClient $salesClient): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SalesClient $salesClient): bool
    {
        return $user->isAdmin();
    }
}
