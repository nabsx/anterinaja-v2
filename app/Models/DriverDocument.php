<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'document_type',
        'document_path',
    ];

    // Relationships
    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }

    // Accessors
    public function getDocumentUrlAttribute()
    {
        return asset('storage/' . $this->document_path);
    }

    // Methods
    public function getDocumentTypeLabel()
    {
        $types = [
            'ktp' => 'KTP',
            'sim' => 'SIM',
            'stnk' => 'STNK',
            'photo' => 'Foto',
        ];

        return $types[$this->document_type] ?? $this->document_type;
    }
}