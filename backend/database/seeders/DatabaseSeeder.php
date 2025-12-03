<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;
use App\Models\Service;
use App\Models\BankDetail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'diamondlipaz@gmail.com',
            'password' => Hash::make('password'), // Change this!
        ]);

        // Seed Payment Methods
        $paymentMethods = [
            [
                'name' => 'Direct Bank Transfer',
                'code' => 'bank_transfer',
                'description' => 'Transfer directly to our bank account',
                'sort_order' => 1,
            ],
            [
                'name' => 'EFT (Electronic Funds Transfer)',
                'code' => 'eft',
                'description' => 'Instant or standard EFT payment',
                'sort_order' => 2,
            ],
            [
                'name' => 'Cash Payment',
                'code' => 'cash',
                'description' => 'Pay cash on arrival at the studio',
                'sort_order' => 3,
            ],
            [
                'name' => 'Cash Send',
                'code' => 'cash_send',
                'description' => 'FNB eWallet, Absa CashSend, Standard Bank Instant Money, etc.',
                'sort_order' => 4,
            ],
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create($method);
        }

        // Seed Services
        $services = [
            [
                'name' => 'Recording Session',
                'description' => 'Professional studio recording session',
                'duration_value' => 3,
                'duration_unit' => 'hours',
                'base_price' => 500.00,
            ],
            [
                'name' => 'Full Project',
                'description' => 'Complete project recording and production',
                'duration_value' => 5,
                'duration_unit' => 'days',
                'base_price' => 5000.00,
            ],
            [
                'name' => 'Album Production',
                'description' => 'Full album recording and production package',
                'duration_value' => 2,
                'duration_unit' => 'weeks',
                'base_price' => 15000.00,
            ],
            [
                'name' => 'Video Shoot',
                'description' => 'Professional music video shooting',
                'duration_value' => 1,
                'duration_unit' => 'day',
                'base_price' => 3000.00,
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }

        // Seed Bank Details (PLACEHOLDER - Update these!)
        BankDetail::create([
            'account_holder_name' => 'LiveNetStudios',
            'bank_name' => 'YOUR BANK NAME',
            'account_number' => '1234567890',
            'branch_code' => '123456',
            'cash_send_mobile' => '+27764556648',
        ]);
    }
}
