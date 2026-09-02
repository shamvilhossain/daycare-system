<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web'; // matches the seeder

    protected $fillable = ['email', 'password', 'role', 'is_active'];
    protected $hidden = ['password', 'remember_token'];

  
    protected function casts(): array
    {
        return [
            'is_active'         => 'boolean',
            'last_login'        => 'datetime',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function parentProfile()
    {
        return $this->hasOne(ParentProfile::class, 'user_id');
    }

    public function staffProfile()
    {
        return $this->hasOne(Staff::class, 'user_id');
    }

    public function getProfileAttribute()
    {
        return $this->parentProfile ?? $this->staffProfile;
    }

    public function getNameAttribute(): string
    {
        if ($this->parentProfile) {
            return $this->parentProfile->full_name;
        }

        if ($this->staffProfile) {
            return $this->staffProfile->full_name;
        }

        return explode('@', $this->email)[0];
    }
}
