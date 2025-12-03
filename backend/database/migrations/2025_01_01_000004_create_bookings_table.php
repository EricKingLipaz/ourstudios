<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference')->unique(); // LNS-2025-00145
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            
            // Customer Information
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            
            // Booking Details
            $table->dateTime('booking_start');
            $table->dateTime('booking_end');
            $table->decimal('total_amount', 10, 2);
            
            // Status
            $table->enum('payment_status', ['pending', 'paid', 'overdue', 'cancelled'])->default('pending');
            $table->enum('booking_status', ['confirmed', 'pending', 'cancelled', 'completed'])->default('pending');
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
