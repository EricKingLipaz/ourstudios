<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BankDetail;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    /**
     * Get active bank details (Public)
     */
    public function getBankDetails()
    {
        $bankDetails = BankDetail::getActive();
        
        if (!$bankDetails) {
            return response()->json(['error' => 'Bank details not configured'], 404);
        }

        return response()->json($bankDetails);
    }

    /**
     * Update bank details (Admin)
     */
    public function updateBankDetails(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'account_holder_name' => 'sometimes|string|max:255',
            'bank_name' => 'sometimes|string|max:255',
            'account_number' => 'sometimes|string|max:255',
            'branch_code' => 'sometimes|string|max:255',
            'cash_send_mobile' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $bankDetail = BankDetail::findOrFail($id);
        $bankDetail->update($request->all());

        return response()->json([
            'message' => 'Bank details updated successfully',
            'bank_details' => $bankDetail,
        ]);
    }

    /**
     * Get all payment methods (Public)
     */
    public function getPaymentMethods()
    {
        $methods = PaymentMethod::active()->get();
        return response()->json($methods);
    }
}
