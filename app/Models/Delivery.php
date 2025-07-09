<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'item_description',
        'item_weight',
        'recipient_name',
        'recipient_phone',
        'special_instructions',
        'item_photo',
    ];

    protected $casts = [
        'item_weight' => 'decimal:2',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Accessors
    public function getItemPhotoUrlAttribute()
    {
        if ($this->item_photo) {
            return asset('storage/' . $this->item_photo);
        }
        return null;
    }

    // Methods
    public function hasPhoto()
    {
        return !empty($this->item_photo);
    }
}
