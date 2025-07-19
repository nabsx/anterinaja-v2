<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'profile_picture',
        'is_active',
        'last_login_at',
        'address',
        'city',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'remember_token',
        'created_at',
        'update_at',
        'deleted_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    // Relationships
    public function driver()
    {
        return $this->hasOne(Driver::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'customer_id');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCustomers($query)
    {
        return $query->where('role', 'customer');
    }

    public function scopeDrivers($query)
    {
        return $query->where('role', 'driver');
    }

    // Accessors
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return asset('images/default-avatar.png');
    }

    public function getFullNameAttribute()
    {
        return $this->name;
    }

    // Methods
    public function isCustomer()
    {
        return $this->role === 'customer';
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function updateLastLogin()
    {
        $this->update(['last_login_at' => now()]);
    }

    public function verifyPhone()
    {
        $this->update(['phone_verified_at' => now()]);
    }

    public function isPhoneVerified()
    {
        return !is_null($this->phone_verified_at);
    }
}
