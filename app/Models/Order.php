<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

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
        'distance_km',
        'estimated_duration',
        'fare_amount',
        'notes',
        'status',
        'accepted_at',
        'picked_up_at',
        'completed_at',
        'cancelled_at',
    ];

    protected $casts = [
        'pickup_latitude' => 'decimal:7',
        'pickup_longitude' => 'decimal:7',
        'destination_latitude' => 'decimal:7',
        'destination_longitude' => 'decimal:7',
        'distance_km' => 'decimal:2',
        'estimated_duration' => 'integer',
        'fare_amount' => 'integer',
        'commission' => 'integer',
        'driver_earning' => 'integer',
        'accepted_at' => 'datetime',
        'picked_up_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    // Boot method untuk auto generate order code
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (!$order->order_code) {
                $order->order_code = 'ANTER-' . strtoupper(Str::random(8));
            }
        });
    }

    // Relationships
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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeRideOrders($query)
    {
        return $query->where('order_type', 'ride');
    }

    public function scopeDeliveryOrders($query)
    {
        return $query->where('order_type', 'delivery');
    }

    public function scopeByCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId);
    }

    public function scopeByDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId);
    }

    // Methods
    public function accept($driverId)
    {
        $this->update([
            'driver_id' => $driverId,
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);

        $this->addTracking('accepted', 'Order accepted by driver');
    }

    public function pickup()
    {
        $this->update([
            'status' => 'picked_up',
            'picked_up_at' => now(),
        ]);

        $this->addTracking('picked_up', 'Customer/item picked up');
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        $this->addTracking('completed', 'Order completed');
        
        // Update driver stats
        if ($this->driver) {
            $this->driver->increment('total_trips');
            $this->driver->setAvailable();
        }
    }

    public function cancel($reason = null)
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        $this->addTracking('cancelled', $reason ?: 'Order cancelled');

        // Set driver back to available
        if ($this->driver) {
            $this->driver->setAvailable();
        }
    }

    public function addTracking($status, $notes = null, $lat = null, $lng = null)
    {
        $this->trackings()->create([
            'status' => $status,
            'notes' => $notes,
            'latitude' => $lat,
            'longitude' => $lng,
        ]);
    }

    public function isRide()
    {
        return $this->order_type === 'ride';
    }

    public function isDelivery()
    {
        return $this->order_type === 'delivery';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isAccepted()
    {
        return $this->status === 'accepted';
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'Menunggu Driver',
            'accepted' => 'Driver Menuju Lokasi',
            'driver_arrived' => 'Driver Telah Tiba',
            'picked_up' => 'Dalam Perjalanan',
            'in_progress' => 'Sedang Berlangsung',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }
}
