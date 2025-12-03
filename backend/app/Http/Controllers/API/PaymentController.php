<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Booking;
use App\Mail\PaymentVerifiedMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    /**
     * Upload proof of payment
     */
    public function uploadProof(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => 'required|exists:bookings,id',
            'proof_of_payment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'cash_send_reference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $booking = Booking::findOrFail($request->booking_id);
        $payment = $booking->payment;

        if (!$payment) {
            return response()->json(['error' => 'Payment record not found'], 404);
        }

        // Store proof of payment
        $path = $request->file('proof_of_payment')->store('proofs-of-payment', 'public');

        // Update payment with proof
        $payment->update([
            'proof_of_payment_path' => $path,
            'cash_send_reference' => $request->cash_send_reference,
        ]);

        return response()->json([
            'message' => 'Proof of payment uploaded successfully',
            'payment' => $payment,
        ]);
    }

    /**
     * Verify payment (Admin only)
     */
    public function verify(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Payment::with('booking')->findOrFail($id);
        
        // Verify payment (assuming authenticated admin user ID = 1)
        $adminId = $request->user() ? $request->user()->id : 1;
        $payment->verify($adminId, $request->admin_notes);

        // Send verification email
        Mail::to($payment->booking->customer_email)
            ->send(new PaymentVerifiedMail($payment->booking));

        return response()->json([
            'message' => 'Payment verified successfully',
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Reject payment (Admin only)
     */
    public function reject(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'admin_notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $payment = Payment::findOrFail($id);
        
        // Reject payment
        $adminId = $request->user() ? $request->user()->id : 1;
        $payment->reject($adminId, $request->admin_notes);

        return response()->json([
            'message' => 'Payment rejected',
            'payment' => $payment->fresh(),
        ]);
    }

    /**
     * Get all payments with filters (Admin)
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.service', 'booking.paymentMethod', 'verifier']);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json($payments);
    }

    /**
     * Export payment report (Admin)
     */
    public function export(Request $request)
    {
        $query = Payment::with(['booking.service', 'booking.paymentMethod']);

        // Apply filters
        if ($request->has('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $payments = $query->get();

        // Generate CSV
        $csv = "Booking Reference,Customer Name,Service,Payment Method,Amount,Status,Verified At\n";
        
        foreach ($payments as $payment) {
            $csv .= sprintf(
                "%s,%s,%s,%s,%s,%s,%s\n",
                $payment->booking->booking_reference,
                $payment->booking->customer_name,
                $payment->booking->service->name,
                $payment->payment_method,
                $payment->amount,
                $payment->status,
                $payment->verified_at ? $payment->verified_at->format('Y-m-d H:i') : 'N/A'
            );
        }

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="payment_report.csv"');
    }
}
