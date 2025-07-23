<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'username',
        'password',
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
        ];
    }

    protected $appends = ['profile', 'name'];

    protected function profile(): Attribute
    {
        return Attribute::get(fn () => $this->getProfile());
    }

    protected function name(): Attribute
    {
        return Attribute::get(fn () => $this->profile->nama);
    }

    protected function rolename(): Attribute
    {
        return Attribute::get(fn () => $this->getRole()->name);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function pegawai(): HasOne
    {
        return $this->hasOne(Pegawai::class);
    }

    public function getProfile()
    {
        return match ($this->getRole()?->name) {
            'admin' => $this->admin,
            'pegawai' => $this->pegawai,
            default => null,
        };
    }

    public function getRole()
    {
        return $this->roles()->first();
    }
}
