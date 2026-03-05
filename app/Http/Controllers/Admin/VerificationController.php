<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class VerificationController extends Controller
{
    public function index(): JsonResponse
    {
        $pending = User::query()
            ->where('hu_role', 'community')
            ->where('hu_verification_status', 'pending')
            ->get(['hu_id', 'hu_name', 'hu_email', 'hu_phone', 'hu_profile_photo_path', 'hu_selfie_media_path']);

        return response()->json([
            'success' => true,
            'message' => 'Pending verification users fetched successfully.',
            'pending' => $pending,
        ]);
    }

    public function showDocument(User $user)
    {
        if (!$user->hu_verification_document_path || !Storage::disk('local')->exists($user->hu_verification_document_path)) {
            abort(404, 'Document not found.');
        }
        return response()->file(Storage::disk('local')->path($user->hu_verification_document_path));
    }

    public function showDocumentById($id)
    {
        $user = User::findOrFail($id);
        if (!$user->hu_verification_document_path || !Storage::disk('local')->exists($user->hu_verification_document_path)) {
            abort(404, 'Document not found.');
        }
        return response()->file(Storage::disk('local')->path($user->hu_verification_document_path));
    }

    public function showSelfie(User $user)
    {
        if (!$user->hu_selfie_media_path || !Storage::disk('local')->exists($user->hu_selfie_media_path)) {
            abort(404, 'Selfie not found.');
        }
        return response()->file(Storage::disk('local')->path($user->hu_selfie_media_path));
    }

    public function approve(User $user)
    {
        // RETENTION POLICY: Keep documents for Audit Trail
        // Do NOT delete files. Do NOT clear DB paths.

        $user->update([
            'hu_verification_status' => 'approved',
            'hu_public_verified_at' => now(),
            // 'verification_document_path' => null, // KEEP REFERENCE
            // 'selfie_media_path' => null, // KEEP REFERENCE
        ]);

        return redirect()->back()->with('success', 'User approved. Documents retained for audit.');
    }

    public function reject(User $user)
    {
        // RETENTION POLICY: Keep documents for Audit Trail
        // Do NOT delete files.

        $user->update([
            'hu_verification_status' => 'rejected',
            // 'verification_document_path' => null, // KEEP REFERENCE
            // 'selfie_media_path' => null, // KEEP REFERENCE
        ]);

        return redirect()->back()->with('success', 'User rejected. Documents retained for audit.');
    }
}
