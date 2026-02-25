<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\StudentService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_request_id' => 'required|exists:service_requests,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $revieweeId = null;
        $studentServiceId = null;

        // --- SERVICE REQUEST LOGIC ---
        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);
        
        if ($serviceRequest->requester_id != Auth::id() && $serviceRequest->provider_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        if (!$serviceRequest->isCompleted()) {
            return response()->json(['error' => 'Service request must be completed before reviewing'], 400);
        }
        
        // Capture Service ID
        $studentServiceId = $serviceRequest->student_service_id;

        // Determine who is being reviewed
        if ($serviceRequest->requester_id == Auth::id()) {
            $revieweeId = $serviceRequest->provider_id;
        } else {
            $revieweeId = $serviceRequest->requester_id;
        }

        // Check if user has already reviewed
        $existingReview = Review::where('reviewer_id', Auth::id())
            ->where('service_request_id', $request->service_request_id)
            ->first();

        if ($existingReview) {
            return response()->json(['error' => 'You have already reviewed this'], 400);
        }

        // Create Review
        $review = Review::create([
            'service_request_id' => $request->service_request_id,
            'student_service_id' => $studentServiceId, // Variable ini sekarang dah ada value
            'reviewer_id' => Auth::id(),
            'reviewee_id' => $revieweeId,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Review submitted successfully',
            'review' => $review
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'reply' => 'required|string|max:1000',
        ]);

    $review = Review::findOrFail($id);

        // Pastikan hanya helper yang berkaitan boleh reply
        if ($review->reviewee_id != Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $review->update([
            'reply' => $request->reply,
            'replied_at' => now(),
        ]);

        return back()->with('success', 'Reply submitted successfully!');
    }
}