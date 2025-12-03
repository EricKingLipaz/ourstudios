<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\BankDetail;
use App\Mail\BookingConfirmationMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Get all bookings (Admin only)
     */
    public function index(Request $request)
    {
        $query = Booking::with(['service', 'paymentMethod', 'payment']);

        // Filter by payment status
        if ($request->has('payment_status')) {
            $query->paymentStatus($request->payment_status);
        }

        // Filter by booking status
        if ($request->has('booking_status')) {
            $query->bookingStatus($request->booking_status);
        }

        // Filter by payment method
        if ($request->has('payment_method_id')) {
            $query->where('payment_method_id', $request->payment_method_id);
        }

        // Search by reference or customer name
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('booking_reference', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($bookings);
    }

    /**
     * Create a new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:services,id',
            'payment_method_id' => 'required|exists:payment_methods,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|string',
            'booking_start' => 'required|date|after:now',
            'booking_end' => 'required|date|after:booking_start',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Get service to calculate price
        $service = \App\Models\Service::findOrFail($request->service_id);

        // Create booking
        $booking = Booking::create([
            'service_id' => $request->service_id,
            'payment_method_id' => $request->payment_method_id,
            'customer_name' => $request->customer_name,
            'customer_email' => $request->customer_email,
            'customer_phone' => $request->customer_phone,
            'booking_start' => $request->booking_start,
            'booking_end' => $request->booking_end,
            'total_amount' => $service->base_price,
            'notes' => $request->notes,
        ]);

        // Create payment record
        $paymentMethod = \App\Models\PaymentMethod::find($request->payment_method_id);
        Payment::create([
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'payment_method' => $paymentMethod->name,
        ]);

        // Load relationships
        $booking->load(['service', 'paymentMethod', 'payment']);

        // Send confirmation emails
        $bankDetails = BankDetail::getActive();
        Mail::to($booking->customer_email)
            ->cc('diamondlipaz@gmail.com')
            ->send(new BookingConfirmationMail($booking, $bankDetails));

        return response()->json([
            'message' => 'Booking created successfully',
            'booking' => $booking,
        ], 201);
    }

    /**
     * Get a specific booking
     */
    public function show($id)
    {
        $booking = Booking::with(['service', 'paymentMethod', 'payment'])->findOrFail($id);
        return response()->json($booking);
    }

    /**
     * Update a booking
     */
    public function update(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'booking_start' => 'sometimes|date',
            'booking_end' => 'sometimes|date|after:booking_start',
            'booking_status' => 'sometimes|in:confirmed,pending,cancelled,completed',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking->update($request->only([
            'booking_start',
            'booking_end',
            'booking_status',
            'notes',
        ]));

        return response()->json([
            'message' => 'Booking updated successfully',
            'booking' => $booking,
        ]);
    }

    /**
     * Cancel a booking
     */
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);
        $booking->update(['booking_status' => 'cancelled']);
        $booking->delete(); // Soft delete

        return response()->json([
            'message' => 'Booking cancelled successfully',
        ]);
    }

    /**
     * Get booking statistics (Admin)
     */
    public function statistics()
    {
        $stats = [
            'total_bookings' => Booking::count(),
            'pending_payment' => Booking::paymentStatus('pending')->count(),
            'paid' => Booking::paymentStatus('paid')->count(),
            'overdue' => Booking::paymentStatus('overdue')->count(),
            'total_revenue' => Booking::paymentStatus('paid')->sum('total_amount'),
        ];

        return response()->json($stats);
    }
}
