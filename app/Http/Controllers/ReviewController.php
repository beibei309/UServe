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
            'service_request_id' => 'required|exists:h2u_service_requests,hsr_id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $revieweeId = null;
        $studentServiceId = null;

        // --- SERVICE REQUEST LOGIC ---
        $serviceRequest = ServiceRequest::findOrFail($request->service_request_id);
        
        if ($serviceRequest->hsr_requester_id != Auth::id() && $serviceRequest->hsr_provider_id != Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => 'Unauthorized',
            ], 403);
        }
        
        if (!$serviceRequest->isCompleted()) {
            return response()->json([
                'success' => false,
                'message' => 'Service request must be completed before reviewing',
                'error' => 'Service request must be completed before reviewing',
            ], 400);
        }
        
        // Capture Service ID
        $studentServiceId = $serviceRequest->hsr_student_service_id;

        // Determine who is being reviewed
        if ($serviceRequest->hsr_requester_id == Auth::id()) {
            $revieweeId = $serviceRequest->hsr_provider_id;
        } else {
            $revieweeId = $serviceRequest->hsr_requester_id;
        }

        // Check if user has already reviewed
        $existingReview = Review::where('hr_reviewer_id', Auth::id())
            ->where('hr_service_request_id', $request->service_request_id)
            ->first();

        if ($existingReview) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reviewed this',
                'error' => 'You have already reviewed this',
            ], 400);
        }

        // Create Review
        $review = Review::create([
            'hr_service_request_id' => $request->service_request_id,
            'hr_student_service_id' => $studentServiceId, // Variable ini sekarang dah ada value
            'hr_reviewer_id' => Auth::id(),
            'hr_reviewee_id' => $revieweeId,
            'hr_rating' => $request->rating,
            'hr_comment' => $request->comment,
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

        if (!ctype_digit((string) $id)) {
            return back()->with('error', 'Invalid review ID.');
        }

    $review = Review::findOrFail((int) $id);

        // Pastikan hanya helper yang berkaitan boleh reply
        if ($review->hr_reviewee_id != Auth::id()) {
            return back()->with('error', 'Unauthorized');
        }

        $review->update([
            'hr_reply' => $request->reply,
            'hr_replied_at' => now(),
        ]);

        return back()->with('success', 'Reply submitted successfully!');
    }
}