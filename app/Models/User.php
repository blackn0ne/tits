<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function hasRole(UserRole $role): bool
    {
        return $this->role === $role;
    }

    public function isAdmin(): bool
    {
        return $this->role->isAdmin();
    }

    public function isUser(): bool
    {
        return $this->role->isUser();
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeRole(Builder $query, UserRole $role): Builder
    {
        return $query->where('role', $role);
    }

    /**
     * @param  Builder<User>  $query
     * @return Builder<User>
     */
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->where('role', UserRole::Admin);
    }
}
