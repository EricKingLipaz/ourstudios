<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'duration_value',
        'duration_unit',
        'base_price',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Get all bookings for this service
     */
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Scope to get only active services
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDurationAttribute()
    {
        return "{$this->duration_value} {$this->duration_unit}";
    }
}
