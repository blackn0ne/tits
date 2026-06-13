<?php

namespace App\Policies;

use App\Models\SalesProduct;
use App\Models\User;

class SalesProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SalesProduct $salesProduct): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, SalesProduct $salesProduct): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SalesProduct $salesProduct): bool
    {
        return $user->isAdmin();
    }
}
