<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_holder_name',
        'bank_name',
        'account_number',
        'branch_code',
        'cash_send_mobile',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the active bank details
     */
    public static function getActive()
    {
        return self::where('is_active', true)->first();
    }
}
