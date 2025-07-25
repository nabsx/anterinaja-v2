<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Driver extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_brand',
        'vehicle_model',
        'vehicle_year',
        'vehicle_plate',
        'license_number',
        'is_verified',
        'is_online',
        'status',
        'current_latitude',
        'current_longitude',
        'last_active_at',
        'rating',
        'total_trips',
        'balance',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_online' => 'boolean',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
        'rating' => 'decimal:2',
        'balance' => 'decimal:2',
        'last_active_at' => 'datetime',
        'verified_at' => 'datetime',
        'vehicle_year' => 'integer',
    ];

    protected $dates = ['deleted_at'];

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

    // Scopes
    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function scopeOffline($query)
    {
        return $query->where('is_online', false);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeBusy($query)
    {
        return $query->where('status', 'busy');
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeUnverified($query)
    {
        return $query->where('is_verified', false);
    }

    public function scopeNearby($query, $latitude, $longitude, $radiusKm = 5)
    {
        return $query->whereRaw(
            "ST_Distance_Sphere(
                point(current_longitude, current_latitude),
                point(?, ?)
            ) <= ?",
            [$longitude, $latitude, $radiusKm * 1000]
        );
    }

    public function scopeWithSufficientBalance($query, $requiredAmount)
    {
        return $query->where('balance', '>=', $requiredAmount);
    }

    // Accessors
    public function getVehicleInfoAttribute()
    {
        return "{$this->vehicle_brand} {$this->vehicle_model} ({$this->vehicle_year})";
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'offline' => 'Offline',
            'available' => 'Tersedia',
            'busy' => 'Sibuk',
            'inactive' => 'Tidak Aktif',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }

    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    public function getBalanceStatusAttribute()
    {
        if ($this->balance <= 0) {
            return 'insufficient';
        } elseif ($this->balance < 50000) { // Warning threshold
            return 'low';
        } else {
            return 'sufficient';
        }
    }

    public function getBalanceStatusLabelAttribute()
    {
        $labels = [
            'insufficient' => 'Saldo Tidak Cukup',
            'low' => 'Saldo Rendah',
            'sufficient' => 'Saldo Cukup'
        ];

        return $labels[$this->balance_status] ?? 'Unknown';
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

        // Haversine formula
        $earthRadius = 6371; // km

        $latDelta = deg2rad($lat - $this->current_latitude);
        $lngDelta = deg2rad($lng - $this->current_longitude);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($this->current_latitude)) * cos(deg2rad($lat)) *
             sin($lngDelta / 2) * sin($lngDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Add balance (for top-ups)
     */
    public function addBalance($amount)
    {
        $this->increment('balance', $amount);
        
        \Log::info('Driver balance topped up', [
            'driver_id' => $this->id,
            'amount_added' => $amount,
            'new_balance' => $this->fresh()->balance
        ]);
    }

    /**
     * Deduct balance (for commission payments)
     */
    public function deductBalance($amount)
    {
        if ($this->balance >= $amount) {
            $this->decrement('balance', $amount);
            
            \Log::info('Driver balance deducted', [
                'driver_id' => $this->id,
                'amount_deducted' => $amount,
                'remaining_balance' => $this->fresh()->balance
            ]);
            
            return true;
        }
        
        \Log::warning('Insufficient balance for deduction', [
            'driver_id' => $this->id,
            'current_balance' => $this->balance,
            'attempted_deduction' => $amount
        ]);
        
        return false;
    }

    /**
     * Check if driver has sufficient balance for commission
     */
    public function hasSufficientBalance($requiredAmount)
    {
        return $this->balance >= $requiredAmount;
    }

    /**
     * Get minimum balance required to go online
     */
    public function getMinimumBalanceRequired()
    {
        // You can set this based on your business logic
        // For example, minimum balance should cover at least 2-3 average commissions
        return 10000; // Rp 10,000
    }

    /**
     * Check if driver can go online (has minimum balance)
     */
    public function canGoOnline()
    {
        return $this->is_verified && 
               $this->balance >= $this->getMinimumBalanceRequired() &&
               $this->hasRequiredDocuments();
    }

    public function incrementTrips()
    {
        $this->increment('total_trips');
    }

    public function isActive()
    {
        return $this->last_active_at && $this->last_active_at->diffInMinutes(now()) <= 30;
    }

    public function verify($notes = null, $verifiedBy = null)
    {
        $this->update([
            'is_verified' => true,
            'verified_at' => now(),
            'verification_notes' => $notes,
            'verified_by' => $verifiedBy,
        ]);
    }

    public function reject($notes = null)
    {
        $this->update([
            'is_verified' => false,
            'verification_notes' => $notes,
        ]);
    }

    public function hasRequiredDocuments()
    {
        $requiredTypes = ['ktp', 'sim', 'stnk', 'photo'];
        $uploadedTypes = $this->documents()->pluck('document_type')->toArray();
        
        return count(array_intersect($requiredTypes, $uploadedTypes)) === count($requiredTypes);
    }
    
    public function vehicleType()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type', 'name');
    }
}
