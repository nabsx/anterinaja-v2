<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'driver_id',
        'rated_by',
        'rating',
        'review',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Scopes
    public function scopeByCustomer($query)
    {
        return $query->where('rated_by', 'customer');
    }

    public function scopeByDriver($query)
    {
        return $query->where('rated_by', 'driver');
    }

    public function scopeForDriver($query, $driverId)
    {
        return $query->where('driver_id', $driverId)->where('rated_by', 'customer');
    }

    public function scopeForCustomer($query, $customerId)
    {
        return $query->where('customer_id', $customerId)->where('rated_by', 'driver');
    }

    // Methods
    public function isCustomerRating()
    {
        return $this->rated_by === 'customer';
    }

    public function isDriverRating()
    {
        return $this->rated_by === 'driver';
    }
}