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
            ->where('role', 'community')
            ->where('verification_status', 'pending')
            ->get(['id', 'name', 'email', 'phone', 'profile_photo_path', 'selfie_media_path']);

        return response()->json(['pending' => $pending]);
    }

    public function showDocument(User $user)
    {
        if (!$user->verification_document_path || !Storage::disk('local')->exists($user->verification_document_path)) {
            abort(404, 'Document not found.');
        }
        return Storage::disk('local')->response($user->verification_document_path);
    }

    public function showSelfie(User $user)
    {
        if (!$user->selfie_media_path || !Storage::disk('local')->exists($user->selfie_media_path)) {
            abort(404, 'Selfie not found.');
        }
        return Storage::disk('local')->response($user->selfie_media_path);
    }

    public function approve(User $user)
    {
        // RETENTION POLICY: Keep documents for Audit Trail
        // Do NOT delete files. Do NOT clear DB paths.

        $user->update([
            'verification_status' => 'approved',
            'public_verified_at' => now(),
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
            'verification_status' => 'rejected',
            // 'verification_document_path' => null, // KEEP REFERENCE
            // 'selfie_media_path' => null, // KEEP REFERENCE
        ]);

        return redirect()->back()->with('success', 'User rejected. Documents retained for audit.');
    }
}