<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Get all appointments for pets owned by the authenticated user
        $appointments = Appointment::whereHas('pet', function ($query) {
            $query->where('user_id', Auth::id());
        })->with(['pet', 'service'])->get();
        
        return response()->json([
            'success' => true,
            'data' => $appointments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'appointment_datetime' => 'required|date',
            'status' => 'nullable|in:scheduled,completed,cancelled,no-show',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that the pet belongs to the authenticated user
        $pet = Pet::where('id', $request->pet_id)
            ->where('user_id', Auth::id())
            ->first();

        if (!$pet) {
            return response()->json([
                'success' => false,
                'message' => 'Pet not found or does not belong to you'
            ], 403);
        }

        $appointment = Appointment::create([
            'pet_id' => $request->pet_id,
            'service_id' => $request->service_id,
            'appointment_datetime' => $request->appointment_datetime,
            'status' => $request->status ?? 'scheduled',
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'data' => $appointment->load(['pet', 'service']),
            'message' => 'Appointment created successfully'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $appointment = Appointment::with(['pet', 'service'])->find($id);
        
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        // Verify that the appointment's pet belongs to the authenticated user
        if ($appointment->pet->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this appointment'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $appointment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $appointment = Appointment::with('pet')->find($id);
        
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        // Verify that the appointment's pet belongs to the authenticated user
        if ($appointment->pet->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this appointment'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'appointment_datetime' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled,no-show',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        // Verify that the new pet belongs to the authenticated user
        if ($request->pet_id != $appointment->pet_id) {
            $pet = Pet::where('id', $request->pet_id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$pet) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pet not found or does not belong to you'
                ], 403);
            }
        }

        $appointment->update([
            'pet_id' => $request->pet_id,
            'service_id' => $request->service_id,
            'appointment_datetime' => $request->appointment_datetime,
            'status' => $request->status,
            'notes' => $request->notes,
        ]);

        return response()->json([
            'success' => true,
            'data' => $appointment->fresh(['pet', 'service']),
            'message' => 'Appointment updated successfully'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $appointment = Appointment::with('pet')->find($id);
        
        if (!$appointment) {
            return response()->json([
                'success' => false,
                'message' => 'Appointment not found'
            ], 404);
        }

        // Verify that the appointment's pet belongs to the authenticated user
        if ($appointment->pet->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized access to this appointment'
            ], 403);
        }

        $appointment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Appointment deleted successfully'
        ]);
    }
}
