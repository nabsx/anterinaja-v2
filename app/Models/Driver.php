<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_plate',
        'license_number',
        'is_verified',
        'current_latitude',
        'current_longitude',
        'is_online',
        'status',
        'rating',
        'total_trips',
        'last_active_at',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_online' => 'boolean',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
        'rating' => 'decimal:2',
        'total_trips' => 'integer',
        'last_active_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function documents()
    {
        return $this->hasMany(DriverDocument::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type', 'name');
    }

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available')->where('is_online', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeNearby($query, $lat, $lng, $radiusKm = 5)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(
                point(current_longitude, current_latitude),
                point(?, ?)
            ) <= ?",
            [$lng, $lat, $radiusKm * 1000]
        );
    }

    // Methods
    public function updateLocation($lat, $lng)
    {
        $this->update([
            'current_latitude' => $lat,
            'current_longitude' => $lng,
            'last_active_at' => now(),
        ]);
    }

    public function setOnline()
    {
        $this->update([
            'is_online' => true,
            'status' => 'available',
            'last_active_at' => now(),
        ]);
    }

    public function setOffline()
    {
        $this->update([
            'is_online' => false,
            'status' => 'offline',
        ]);
    }

    public function setBusy()
    {
        $this->update(['status' => 'busy']);
    }

    public function setAvailable()
    {
        $this->update(['status' => 'available']);
    }

    public function updateRating()
    {
        $avgRating = $this->ratings()
            ->where('rated_by', 'customer')
            ->avg('rating');
        
        $this->update(['rating' => $avgRating ?: 5.00]);
    }

    public function calculateDistanceFrom($lat, $lng)
    {
        if (!$this->current_latitude || !$this->current_longitude) {
            return null;
        }

        $earthRadius = 6371; // km
        $latDiff = deg2rad($lat - $this->current_latitude);
        $lngDiff = deg2rad($lng - $this->current_longitude);
        
        $a = sin($latDiff / 2) * sin($latDiff / 2) +
            cos(deg2rad($this->current_latitude)) * cos(deg2rad($lat)) *
            sin($lngDiff / 2) * sin($lngDiff / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }
}
