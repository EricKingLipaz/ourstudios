<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_id',
        'amount',
        'payment_method',
        'proof_of_payment_path',
        'cash_send_reference',
        'status',
        'verified_by',
        'verified_at',
        'admin_notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'verified_at' => 'datetime',
    ];

    /**
     * Get the booking for this payment
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Get the admin who verified this payment
     */
    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Get the full URL for proof of payment
     */
    public function getProofOfPaymentUrlAttribute()
    {
        if ($this->proof_of_payment_path) {
            return Storage::url($this->proof_of_payment_path);
        }
        return null;
    }

    /**
     * Verify the payment
     */
    public function verify($adminId, $notes = null)
    {
        $this->update([
            'status' => 'verified',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $notes,
        ]);

        // Update booking payment status
        $this->booking->update(['payment_status' => 'paid']);
    }

    /**
     * Reject the payment
     */
    public function reject($adminId, $notes)
    {
        $this->update([
            'status' => 'rejected',
            'verified_by' => $adminId,
            'verified_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}
