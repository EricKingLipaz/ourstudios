<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    /**
     * Get all active services
     */
    public function index()
    {
        $services = Service::active()->get();
        return response()->json($services);
    }

    /**
     * Get all services (Admin - including inactive)
     */
    public function all()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Create a new service (Admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration_value' => 'required|integer|min:1',
            'duration_unit' => 'required|in:hours,days,weeks',
            'base_price' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::create($request->all());

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service,
        ], 201);
    }

    /**
     * Update a service (Admin)
     */
    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'duration_value' => 'sometimes|integer|min:1',
            'duration_unit' => 'sometimes|in:hours,days,weeks',
            'base_price' => 'sometimes|numeric|min:0',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service->update($request->all());

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service,
        ]);
    }

    /**
     * Delete a service (Admin)
     */
    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json([
            'message' => 'Service deleted successfully',
        ]);
    }
}
