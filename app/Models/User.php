<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
        'email',
        'password',
        'role',
        'roles',
    ];

    // Roles: ADMIN, ENTERTAINER, CUSTOMER
    public const ROLE_ADMIN = 'ADMIN';
    public const ROLE_ENTERTAINER = 'ENTERTAINER';
    public const ROLE_CUSTOMER = 'CUSTOMER';

    /**
     * Check user role
     */
    public function hasRole(string $role): bool
    {
        $roles = $this->roles ?? [];
        if (is_string($roles) && $roles !== '') {
            $roles = [$roles];
        }
        return in_array(strtoupper($role), array_map('strtoupper', $roles));
    }

    public function isAdmin(): bool
    {
        if ($this->email === 'yehudaj@gmail.com') return true;
        return $this->hasRole(self::ROLE_ADMIN);
    }

    public function isEntertainer(): bool
    {
        return $this->hasRole(self::ROLE_ENTERTAINER);
    }

    public function entertainer()
    {
        return $this->hasOne(Entertainer::class);
    }

    public function customer()
    {
        return $this->hasOne(Customer::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user');
    }

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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles' => 'array',
        'password' => 'hashed',
    ];

    public function addRole(string $role): void
    {
        $roleModel = \App\Models\Role::firstOrCreate(['name' => strtoupper($role)]);
        if (! $this->roles()->where('role_id', $roleModel->id)->exists()) {
            $this->roles()->attach($roleModel->id);
        }
    }

    public function removeRole(string $role): void
    {
        $roleModel = \App\Models\Role::where('name', strtoupper($role))->first();
        if ($roleModel) {
            $this->roles()->detach($roleModel->id);
        }
    }
}
