<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        'fare_amount',
        'driver_earning',
        'platform_commission',
        'fare_breakdown',
        'notes',
        'status',
        'scheduled_at',
        'accepted_at',
        'picked_up_at',
        'completed_at',
        'cancelled_at',
        'cancelled_by',
        'cancellation_reason'
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'destination_latitude' => 'decimal:7',
        'destination_longitude' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'fare_amount' => 'integer',
        'driver_earning' => 'integer',
        'platform_commission' => 'integer',
        'fare_breakdown' => 'array',
        'scheduled_at' => 'datetime',
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected $dates = ['deleted_at'];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class);
    }

    public function tracking()
    {
        return $this->hasMany(OrderTracking::class);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'Menunggu Driver',
            'accepted' => 'Driver Diterima',
            'driver_arrived' => 'Driver Tiba',
            'picked_up' => 'Dijemput',
            'in_progress' => 'Dalam Perjalanan',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    public function getVehicleTypeLabelAttribute()
    {
        $vehicleLabels = [
            'motorcycle' => 'Motor',
            'car' => 'Mobil',
            'van' => 'Van',
            'truck' => 'Truk'
        ];

        return $vehicleLabels[$this->vehicle_type] ?? $this->vehicle_type;
    }

    public function getOrderTypeLabelAttribute()
    {
        $orderLabels = [
            'ride' => 'Perjalanan',
            'delivery' => 'Pengiriman'
        ];

        return $orderLabels[$this->order_type] ?? $this->order_type;
    }

    public function getFormattedFareAttribute()
    {
        return 'Rp ' . number_format($this->fare_amount, 0, ',', '.');
    }

    public function getFormattedDistanceAttribute()
    {
        return number_format($this->distance_km, 2) . ' km';
    }

    public function getFormattedDurationAttribute()
    {
        return $this->duration_minutes . ' menit';
    }

    // Scopes
    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'accepted', 'driver_arrived', 'picked_up', 'in_progress']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    // Helper methods
    public function canBeCancelled()
    {
        return in_array($this->status, ['pending', 'accepted', 'driver_arrived']);
    }

    public function canBeRated()
    {
        return $this->status === 'completed' && !$this->ratings()->where('rated_by', 'customer')->exists();
    }

    public function isActive()
    {
        return in_array($this->status, ['pending', 'accepted', 'driver_arrived', 'picked_up', 'in_progress']);
    }

    public function addTracking($status, $notes = null)
    {
        return $this->tracking()->create([
            'status' => $status,
            'notes' => $notes,
            'created_at' => now()
        ]);
    }

    // Boot method for model events
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_code)) {
                $order->order_code = 'ORD-' . date('Ymd') . '-' . strtoupper(\Str::random(6));
            }
        });

        static::updated(function ($order) {
            // Add tracking when status changes
            if ($order->isDirty('status')) {
                $order->addTracking($order->status, 'Status changed to ' . $order->status);
            }
        });
    }

    // Methods
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

    private function getDistanceFromOSRM()
    {
        // Placeholder for actual OSRM distance calculation logic
        return 3.75; // Example distance in kilometers
    }
}
