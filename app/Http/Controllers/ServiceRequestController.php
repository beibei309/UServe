<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use App\Notifications\NewServiceRequest;
use App\Notifications\ServiceRequestStatusUpdated;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewServiceRequestNotification;
use Carbon\Carbon;


class ServiceRequestController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new service request
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            
            // 1. Validate basic fields (Make times nullable for flexibility)
            $validated = $request->validate([
                'student_service_id' => 'required|exists:student_services,id',
                'selected_dates'     => 'required|date',
                'start_time'         => 'nullable|string', 
                'end_time'           => 'nullable|string',
                'selected_package'   => 'required|string',
                'message'            => 'nullable|string|max:1000',
                'offered_price'      => 'nullable|numeric|min:0|max:99999.99'
            ]);

            $studentService = StudentService::findOrFail($validated['student_service_id']);

            // Check availability
            if (!$studentService->is_active || !$studentService->user->is_available) {
                return response()->json(['error' => 'Service or provider unavailable.'], 400);
            }

            // Check for existing active requests from this user
           $hasActiveRequest = ServiceRequest::where('requester_id', $user->id)
                ->where('student_service_id', $studentService->id) // <--- CHANGED: Check specific service ID
                ->whereIn('status', ['pending', 'accepted', 'in_progress'])
                ->exists();

            if ($hasActiveRequest) {
                return response()->json(['error' => 'You already have an active request with this helper.'], 400);
            }

            // --- LOGIC SPLIT: Session vs Task ---
            $startTime = $validated['start_time'];
            $endTime   = $validated['end_time'];

            // If it is Session Based, we MUST have times and check overlap
            if ($studentService->session_duration) {
                if (!$startTime || !$endTime) {
                    return response()->json(['error' => 'Start and End time are required for this service.'], 422);
                }

                // Check Overlap logic ONLY for session-based services
                $overlapping = ServiceRequest::where('student_service_id', $studentService->id)
                    ->where('selected_dates', $validated['selected_dates'])
                    ->whereIn('status', ['pending', 'accepted', 'in_progress', 'approved'])
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('start_time', '<', $endTime)
                            ->where('end_time', '>', $startTime);
                    })
                    ->exists();

                if ($overlapping) {
                    return response()->json(['error' => 'This time slot is booked. Please select another.'], 400);
                }
            } else {
                $startTime = $startTime ?? '00:00';
                $endTime   = $endTime ?? '23:59';
                
        
            }

            // Create Request
            $serviceRequest = ServiceRequest::create([
                'student_service_id' => $studentService->id,
                'requester_id'       => $user->id,
                'provider_id'        => $studentService->user_id,
                'selected_dates'     => $validated['selected_dates'],
                'start_time'         => $startTime,
                'end_time'           => $endTime,
                'selected_package'   => json_encode($validated['selected_package']),
                'message'            => $validated['message'],
                'offered_price'      => $validated['offered_price'],
                'status'             => 'pending'
            ]);

            // Notify Provider
            $studentService->user->notify(new NewServiceRequest($serviceRequest));
            // Through email
            if ($studentService->user->email) {
                Mail::to($studentService->user->email)
                    ->send(new NewServiceRequestNotification($serviceRequest, 'provider'));
            }

            // Send Email to Student (Requester)
            if ($user->email) {
                Mail::to($user->email)
                    ->send(new NewServiceRequestNotification($serviceRequest, 'student'));
            }
            

            return response()->json([
                'success' => true,
                'message' => 'Service request sent successfully!',
                'request_id' => $serviceRequest->id
            ]);

        } catch (\Exception $e) {
            \Log::error('ServiceRequest store error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error occurred.'], 500);
        }
    }

public function index(Request $request)
{
    $user = Auth::user();
    
    // --- 1. DATA FOR DROPDOWNS ---
    $categories = \App\Models\Category::all();
    
    // --- 2. CAPTURE INPUTS ---
    $search = $request->input('search');
    $categoryId = $request->input('category');
    $selectedServiceId = $request->input('service_type'); 
    $status = $request->input('status'); // NEW: Capture Status

    // Safety: Reset service ID if it's invalid
    if (is_array($selectedServiceId) || json_decode((string)$selectedServiceId)) {
        $selectedServiceId = null;
    }

    $viewMode = session('view_mode', 'buyer');

    // ==========================================
    // 3. HELPER MODE (Seller View)
    // ==========================================
    if ($user->role === 'helper' && $viewMode === 'seller') {
        
        // Fetch only THIS seller's services for the dropdown
        $myServices = \App\Models\StudentService::where('user_id', $user->id)
                        ->select('id', 'title')
                        ->get();

        $query = \App\Models\ServiceRequest::where('provider_id', $user->id)
            ->with(['requester', 'studentService']);

        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('requester', function($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // B. Category Filter
        if ($categoryId) {
            $query->whereHas('studentService', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // C. Service Type Filter
        if ($selectedServiceId) {
            $query->where('student_service_id', $selectedServiceId);
        }

        // D. Status Filter (NEW)
        if ($status) {
            $query->where('status', $status);
        }

        // Default Sort: Always Newest First
        $query->orderBy('created_at', 'desc');

        $receivedRequests = $query->get();

        return view('service-requests.helper', [
            'receivedRequests' => $receivedRequests,
            'categories' => $categories,
            'serviceTypes' => $myServices 
        ]);
    }

    // ==========================================
    // 4. BUYER MODE (Student View)
    // ==========================================
    else {
        // Buyers see all services in the dropdown
        $allServiceTypes = \App\Models\StudentService::select('id', 'title')->get();

        $query = \App\Models\ServiceRequest::where('requester_id', $user->id)
            ->with(['provider', 'studentService']);

        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('provider', function($subQ) use ($search) {
                    $subQ->where('name', 'LIKE', "%{$search}%");
                });
            });
        }

        // B. Category Filter
        if ($categoryId) {
            $query->whereHas('studentService', function($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        // C. Service Type Filter
        if ($selectedServiceId) {
            $query->where('student_service_id', $selectedServiceId);
        }

        // D. Status Filter (NEW)
        if ($status) {
            $query->where('status', $status);
        }

        // Default Sort: Always Newest First
        $query->orderBy('created_at', 'desc');

        $sentRequests = $query->get();

        return view('service-requests.index', [
            'sentRequests' => $sentRequests,
            'categories' => $categories,
            'serviceTypes' => $allServiceTypes
        ]);
    }
}

    /**
     * Show a specific service request
     */
    public function show(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only requester and provider can view the request
        if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to view this request.');
        }

        $serviceRequest->load([
            'studentService.category', 
            'requester', 
            'provider',
            'reviews.reviewer'
        ]);

        return view('service-requests.show', compact('serviceRequest'));
    }

    /**
     * Accept a service request
     */
    public function accept(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        

        // Only the provider can accept
        if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to accept this request.');
        }

        if (!$serviceRequest->isPending()) {
            return response()->json(['error' => 'This request cannot be accepted.'], 400);
        }

        $serviceRequest->accept();

        // Notify Requester
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'accepted'));

        return response()->json([
            'success' => true,
            'message' => 'Service request accepted successfully!'
        ]);
    }

    /**
     * Reject a service request
     */
    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to reject this request.');
        }

        if (!$serviceRequest->isPending()) {
            return response()->json(['error' => 'This request cannot be rejected.'], 400);
        }

        // 1. VALIDATE: Reason is mandatory
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // 2. UPDATE: Save status AND reason
        $serviceRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason
        ]);

        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'rejected'));

        return back()->with('success', 'Service request rejected.');
    }

    public function markInProgress(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as in progress
       if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isAccepted()) {
            return response()->json(['error' => 'This request must be accepted first.'], 400);
        }

        
        $serviceRequest->update([
            'status' => 'in_progress',
            'started_at' => now(), 
        ]);
       
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'in_progress'));

        return response()->json([
            'success' => true,
            'message' => 'Service marked as in progress!'
        ]);
    }

    public function markWorkFinished(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as in progress
        if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isInProgress()) {
            return response()->json(['error' => 'This request must be in progress first.'], 400);
        }

        
        $serviceRequest->update([
            'status' => 'waiting_payment',
            'finished_at' => now(), 
        ]);
       
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'waiting_payment'));

        return response()->json([
            'success' => true,
            'message' => 'Service marked as finished!'
        ]);
    }

    // BUYER/REQUESTER SIDE TO MAKE PAKMENT
   public function buyerConfirmPayment(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
	$user = auth()->user();
       if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403);
        }

        // 2. Validate (File is optional, but if present must be an image/pdf)
        $request->validate([
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        // 3. Handle File Upload
        if ($request->hasFile('payment_proof')) {
            // Stores in storage/app/public/payment_proofs
            $path = $request->file('payment_proof')->store('payment_proofs', 'public');
            
            // Save path to DB
            $serviceRequest->update(['payment_proof' => $path]);
        }

        // 4. Update Status
        $serviceRequest->update([
            'payment_status' => 'verification_status'
        ]);

        return back()->with('success', 'Payment confirmed! Waiting for seller verification.');
    }

    public function finalizeOrder(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
	$user = auth()->user();
       if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $outcome = $request->input('outcome'); // 'paid' or 'unpaid_problem'

        if ($outcome === 'paid') {
            // ✅ SCENARIO A: Success
            // This updates BOTH status columns at once
            $serviceRequest->update([
                'status' => 'completed',          // Moves order to History
                'payment_status' => 'paid',       // Marks payment as Green/Paid
                'paid_at' => now(),               // Record payment time
                'completed_at' => now(),          // Record completion time
            ]);

            // Notify Buyer
            $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

            return back()->with('success', 'Payment confirmed and Order marked as Completed!');
        } 
        
        else {
            $serviceRequest->update([
                'status' => 'waiting_payment',          // We still close the order
                'payment_status' => 'unpaid', // But flag it as a problem
                'completed_at' => now(),
            ]);

            return back()->with('error', 'Order closed as Unpaid. Buyer reported.');
        }
    }

    // FOR BUYER REPORT
    public function reportIssue(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);

        // Validate
        $request->validate([
            'dispute_reason' => 'required|string',
        ]);

        // Concatenate reason if notes exist
        $reason = $request->dispute_reason;
        if($request->additional_notes) {
            $reason .= " - Note: " . $request->additional_notes;
        }

        // Update status to 'disputed'
        $serviceRequest->update([
            'status' => 'disputed',
            'dispute_reason' => $reason,
            'reported_by' => auth()->id()
        ]);

        return back()->with('success', 'Report submitted. Admin will review the case.');
    }

    public function report(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        // 1. Update the Request Status
        $serviceRequest->update([
            'status' => 'disputed', 
            'payment_status' => 'dispute', 
            'dispute_reason' => $request->reason,
            'reported_by' => auth()->id()
        ]);

        $buyerId = $serviceRequest->requester_id;

        if ($buyerId) {
            \App\Models\User::where('id', $buyerId)->increment('reports_count');
        }

        return back()->with('success', 'Report submitted. Buyer has been flagged.');
    }

    public function cancelDispute($id)
    {
        $request = ServiceRequest::findOrFail($id);

        // Optional: Security check to ensure only the creator of the dispute or admin can do this
        // if (auth()->id() !== $request->user_id) { abort(403); }

        if ($request->status === 'disputed') {
            $request->status = 'completed'; // Set directly to completed as requested
            $request->save();
            
            return back()->with('success', 'Report cancelled. Order marked as completed.');
        }

        return back()->with('error', 'Cannot cancel report at this stage.');
    }

    public function markCompleted(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as completed
        if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isInProgress()) {
            return response()->json(['error' => 'This request must be in progress first.'], 400);
        }

        // --- UBAH KAT SINI ---
        $serviceRequest->update([
            'status' => 'completed',
            'completed_at' => now(), // Rekod masa tamat kerja
        ]);

        // Notify Requester
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

        return response()->json([
            'success' => true,
            'message' => 'Service marked as completed! Both parties can now leave reviews.'
        ]);
    }

        public function markAsPaid($id) {
        $request = ServiceRequest::findOrFail($id);
        $request->update(['is_paid' => true]);
        return back()->with('success', 'Payment status updated.');
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // 1. Authorization
       if ($serviceRequest->requester_id != $user->id && $serviceRequest->provider_id != $user->id) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        // 2. Block if Completed
        if ($serviceRequest->status === 'completed') {
            return response()->json(['error' => 'Completed requests cannot be cancelled.'], 400);
        }

        // 3. Block if In Progress (Seller started work)
        if ($serviceRequest->status === 'in_progress') {
            return response()->json([
                'success' => false,
                'title'   => 'Work Started',
                'message' => 'The seller has already started working on this request. Please contact the seller directly to discuss cancellation.'
            ], 400);
        }

        // 4. Allow Cancellation (for 'pending' or 'accepted')
        $serviceRequest->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Service request cancelled successfully.'
        ]);
    }
        public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        // Validate that the user owns the request or is the provider
        if (auth()->id() !== $serviceRequest->requester_id && auth()->id() !== $serviceRequest->provider_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Validate the new status
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,completed,cancelled,rejected'
        ]);

        // Update the status
        $serviceRequest->update([
            'status' => $validated['status']
        ]);

        return response()->json([
            'success' => true, 
            'message' => 'Booking status updated to ' . $validated['status']
        ]);
    }

    public function storeBuyerReview(Request $request, ServiceRequest $serviceRequest)
    {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:500',
        ]);

        // ensure only provider can review requester
        if ($serviceRequest->provider_id !== auth()->id()) {
            abort(403);
        }

        // prevent duplicate review
        if ($serviceRequest->reviewByHelper) {
            return back()->with('error', 'You already reviewed this client.');
        }

        Review::create([
            'service_request_id' => $serviceRequest->id,
            'reviewer_id' => auth()->id(),
            'reviewee_id' => $serviceRequest->requester_id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted!');
    }

}