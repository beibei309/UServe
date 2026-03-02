<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AvailabilityController extends Controller
{
    public function toggle(): JsonResponse
    {
        $user = request()->user();
        if (!$user || $user->hu_role !== 'helper') {
            return response()->json([
                'success' => false,
                'message' => 'Only authenticated helpers may toggle availability.',
                'error' => 'Only authenticated helpers may toggle availability.',
            ], 403);
        }

        $user->hu_is_available = !$user->hu_is_available;

        // kalau jadi unavailable, kosongkan dulu tarikh supaya frontend set baru
        if (!$user->hu_is_available) {
            $user->hu_unavailable_start_date = null;
            $user->hu_unavailable_end_date = null;
        }

        $user->save();

        return response()->json([
            'success' => true,
            'is_available' => $user->hu_is_available,
            'message' => $user->hu_is_available ? 'Saya Bersedia' : 'Sila pilih tarikh tidak available',
        ]);
    }

    public function setDates(Request $request): JsonResponse
    {
        $user = $request->user();

        $request->validate([
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $user->hu_unavailable_start_date = $request->start_date;
        $user->hu_unavailable_end_date = $request->end_date;
        $user->hu_is_available = false;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Tarikh unavailable telah disimpan.',
            'is_available' => $user->hu_is_available,
            'start_date' => $user->hu_unavailable_start_date,
            'end_date' => $user->hu_unavailable_end_date,
        ]);
    }

   public function updateSettings(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Validate Input
        $validated = $request->validate([
            'is_available' => 'required|boolean',
            'start_date'   => 'nullable|date|required_if:is_available,false', 
            'end_date'     => 'nullable|date|after_or_equal:start_date|required_if:is_available,false',
        ]);
        
        // 2. Update Availability Boolean
        $user->hu_is_available = $validated['is_available'];

        // 3. Handle Dates Logic
        if ($user->hu_is_available) {
            $user->hu_unavailable_start_date = null;
            $user->hu_unavailable_end_date = null;
        } else {
            $user->hu_unavailable_start_date = $validated['start_date'];
            $user->hu_unavailable_end_date = $validated['end_date'];
        }

        $user->save();

        $newServiceStatus = $user->hu_is_available ? 'available' : 'unavailable';

        $user->studentServices()->update([
            'hss_status' => $newServiceStatus
        ]);



        return response()->json([
            'success' => true,
            'message' => 'Availability settings updated successfully.',
            'is_available' => $user->hu_is_available,
            'start_date' => $user->hu_unavailable_start_date,
            'end_date' => $user->hu_unavailable_end_date,
        ]);
    }
}
