<?php

namespace App\Policies;

use App\Models\SalesService;
use App\Models\User;

class SalesServicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SalesService $salesService): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SalesService $salesService): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SalesService $salesService): bool
    {
        return $user->isAdmin();
    }
}
