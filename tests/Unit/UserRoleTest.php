<?php

use App\Enums\UserRole;

test('user role enum has admin and user cases', function () {
    expect(UserRole::cases())->toHaveCount(2)
        ->and(UserRole::Admin->value)->toBe('admin')
        ->and(UserRole::User->value)->toBe('user');
});
