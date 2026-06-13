<?php

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\AdminUserSeeder;

test('user model casts role to enum and provides helpers', function () {
    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();

    expect($admin->role)->toBeInstanceOf(UserRole::class)
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->isUser())->toBeFalse()
        ->and($admin->hasRole(UserRole::Admin))->toBeTrue()
        ->and($user->isAdmin())->toBeFalse()
        ->and($user->isUser())->toBeTrue();
});

test('admin user seeder creates moka admin', function () {
    $this->seed(AdminUserSeeder::class);

    $admin = User::query()->where('username', 'admin')->first();

    expect($admin)->not->toBeNull()
        ->and($admin->name)->toBe('Moka Admin')
        ->and($admin->email)->toBe('mokhamediyar@gmail.com')
        ->and($admin->role)->toBe(UserRole::Admin);
});
