<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_code',
        'customer_id',
        'driver_id',
        'order_type',
        'pickup_address',
        'pickup_latitude',
        'pickup_longitude',
        'destination_address',
        'destination_latitude',
        'destination_longitude',
        'vehicle_type',
        'distance_km',
        'duration_minutes',
        'estimated_fare',
        'actual_fare',
        'fare_breakdown',
        'status',
        'notes',
        'scheduled_at',
        'accepted_at',
        'driver_arrived_at',
        'picked_up_at',
        'started_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason',
        'driver_earning',
        'platform_commission',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:8', // Ubah dari 'pickup_lat'
        'pickup_longitude' => 'decimal:8', // Ubah dari 'pickup_lng'
        'destination_latitude' => 'decimal:8', // Ubah dari 'destination_lat'
        'destination_longitude' => 'decimal:8', // Ubah dari 'destination_lng'
        'distance_km' => 'decimal:2',
        'duration_minutes' => 'decimal:2',
        'estimated_fare' => 'decimal:2',
        'actual_fare' => 'decimal:2',
        'driver_earning' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'fare_breakdown' => 'array',
        'scheduled_at' => 'datetime',
        'accepted_at' => 'datetime',
        'driver_arrived_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function delivery()
    {
        return $this->hasOne(Delivery::class);
    }

    public function trackings()
    {
        return $this->hasMany(OrderTracking::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    public function scopeRideType($query)
    {
    return $query->where('order_type', 'ride');
    }

    public function scopeDeliveryType($query)
    {
    return $query->where('order_type', 'delivery');
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Driver',
            'accepted' => 'Diterima Driver',
            'driver_arrived' => 'Driver Tiba',
            'picked_up' => 'Diambil',
            'in_progress' => 'Dalam Perjalanan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getFormattedFareAttribute()
    {
        return 'Rp ' . number_format($this->estimated_fare, 0, ',', '.');
    }

    public function getFormattedDistanceAttribute()
    {
        return $this->distance_km . ' km';
    }

    public function getFormattedDurationAttribute()
    {
        return $this->duration_minutes . ' menit';
    }

    // Methods
    public function addTracking($status, $notes = null, $lat = null, $lng = null)
    {
        return $this->trackings()->create([
            'status' => $status,
            'notes' => $notes,
            'latitude' => $lat,
            'longitude' => $lng,
            'tracked_at' => now(),
        ]);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function isInProgress()
    {
        return $this->status === 'in_progress';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'accepted', 'driver_arrived']);
    }

    public function canBeRated()
    {
        return $this->status === 'completed' && !$this->ratings()->exists();
    }

    public function calculateActualFare()
    {
        // Override with actual calculation if needed
        return $this->estimated_fare;
    }

    public function updateStatus($status, $additionalData = [])
    {
        $updateData = array_merge(['status' => $status], $additionalData);
        
        switch ($status) {
            case 'accepted':
                $updateData['accepted_at'] = now();
                break;
            case 'driver_arrived':
                $updateData['driver_arrived_at'] = now();
                break;
            case 'picked_up':
                $updateData['picked_up_at'] = now();
                break;
            case 'in_progress':
                $updateData['started_at'] = now();
                break;
            case 'completed':
                $updateData['completed_at'] = now();
                if (!isset($updateData['actual_fare'])) {
                    $updateData['actual_fare'] = $this->calculateActualFare();
                }
                break;
            case 'cancelled':
                $updateData['cancelled_at'] = now();
                break;
        }

        $this->update($updateData);
        $this->addTracking($status, $additionalData['notes'] ?? null);
    }

    public function calculateFare(Request $request, FareCalculatorService $fareService)
    {
        Log::info('CalculateFare called', $request->all());
    $request->validate([
        'pickup_latitude' => 'required|numeric',
        'pickup_longitude' => 'required|numeric',
        'destination_latitude' => 'required|numeric',
        'destination_longitude' => 'required|numeric',
        'service_type' => 'required|in:motor,mobil',
    ]);

    $distanceKm = $this->getDistanceFromOSRM(...); // misalnya: 3.75

    $fare = $fareService->calculate($distanceKm, $request->service_type);

    return response()->json([
        'success' => true,
        'data' => [
            'distance' => round($distanceKm, 2),
            'base_fare' => $fare['base_fare'],
            'distance_fare' => $fare['distance_fare'],
            'commission' => $fare['commission'],
            'driver_earnings' => $fare['total_driver'],
            'total_fare' => $fare['total_customer'],
        ]
    ]);
    }

    public function scopeNearby($query, $latitude, $longitude, $radiusKm = 5)
    {
    return $query->whereRaw(
        "ST_Distance_Sphere(
            point(pickup_longitude, pickup_latitude),
            point(?, ?)
        ) <= ?",
        [$longitude, $latitude, $radiusKm * 1000]
    );
    }
}
