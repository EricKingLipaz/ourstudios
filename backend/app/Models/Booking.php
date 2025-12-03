<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_reference',
        'service_id',
        'payment_method_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_start',
        'booking_end',
        'total_amount',
        'payment_status',
        'booking_status',
        'notes',
    ];

    protected $casts = [
        'booking_start' => 'datetime',
        'booking_end' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /**
     * Boot method to auto-generate booking reference
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($booking) {
            if (empty($booking->booking_reference)) {
                $booking->booking_reference = self::generateBookingReference();
            }
        });
    }

    /**
     * Generate unique booking reference
     */
    public static function generateBookingReference()
    {
        $year = Carbon::now()->format('Y');
        $lastBooking = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $number = $lastBooking ? (int)substr($lastBooking->booking_reference, -5) + 1 : 1;
        
        return 'LNS-' . $year . '-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get the service for this booking
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the payment method
     */
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    /**
     * Get the payment record
     */
    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Scope for filtering by payment status
     */
    public function scopePaymentStatus($query, $status)
    {
        return $query->where('payment_status', $status);
    }

    /**
     * Scope for filtering by booking status
     */
    public function scopeBookingStatus($query, $status)
    {
        return $query->where('booking_status', $status);
    }

    /**
     * Check if payment is overdue (48 hours after creation)
     */
    public function isPaymentOverdue()
    {
        return $this->payment_status === 'pending' 
            && $this->created_at->addHours(48)->isPast();
    }
}
