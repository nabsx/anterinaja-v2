<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'display_name',
        'base_fare',
        'per_km_rate',
        'capacity',
        'icon',
        'is_active',
    ];

    protected $casts = [
        'base_fare' => 'integer',
        'per_km_rate' => 'integer',
        'capacity' => 'integer',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function drivers()
    {
        return $this->hasMany(Driver::class, 'vehicle_type', 'name');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Methods
    public function calculateFare($distanceKm)
    {
        return $this->base_fare + ($distanceKm * $this->per_km_rate);
    }

    public function getIconUrlAttribute()
    {
        if ($this->icon) {
            return asset('storage/' . $this->icon);
        }
        return asset('images/default-vehicle.png');
    }
}
