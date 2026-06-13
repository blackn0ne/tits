<?php

namespace App\Policies;

use App\Models\ProjectCategory;
use App\Models\User;

class ProjectCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, ProjectCategory $projectCategory): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, ProjectCategory $projectCategory): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, ProjectCategory $projectCategory): bool
    {
        return $user->isAdmin();
    }
}
