<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use App\Models\StudentService;
use App\Models\Review;
use App\Models\Cat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
                'student_service_id' => 'required|exists:h2u_student_services,hss_id',
                'selected_dates'     => 'required|date',
                'start_time'         => 'nullable|string', 
                'end_time'           => 'nullable|string',
                'selected_package'   => 'required|string',
                'message'            => 'nullable|string|max:1000',
                'offered_price'      => 'nullable|numeric|min:0|max:99999.99'
            ]);

            $studentService = StudentService::findOrFail($validated['student_service_id']);

            // Check availability
            if (!$studentService->hss_is_active || !$studentService->user->hu_is_available) {
                return response()->json([
                    'success' => false,
                    'message' => 'Service or provider unavailable.',
                ], 400);
            }

            // Check for existing active requests from this user
         $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $user->hu_id)
             ->where('hsr_student_service_id', $studentService->hss_id) // <--- CHANGED: Check specific service ID
             ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
                ->exists();

            if ($hasActiveRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active request with this helper.',
                ], 400);
            }

            // --- LOGIC SPLIT: Session vs Task ---
            $startTime = $validated['start_time'];
            $endTime   = $validated['end_time'];

            // If it is Session Based, we MUST have times and check overlap
            if ($studentService->hss_session_duration) {
                if (!$startTime || !$endTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Start and End time are required for this service.',
                    ], 422);
                }

                // Check Overlap logic ONLY for session-based services
                $selectedDateJson = json_encode($validated['selected_dates']);

                $overlapping = ServiceRequest::where('hsr_student_service_id', $studentService->hss_id)
                    ->whereRaw('hsr_selected_dates::text = ?', [$selectedDateJson])
                    ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress', 'approved'])
                    ->where(function ($query) use ($startTime, $endTime) {
                        $query->where('hsr_start_time', '<', $endTime)
                            ->where('hsr_end_time', '>', $startTime);
                    })
                    ->exists();

                if ($overlapping) {
                    return response()->json([
                        'success' => false,
                        'message' => 'This time slot is booked. Please select another.',
                    ], 400);
                }
            } else {
                $startTime = $startTime ?? '00:00';
                $endTime   = $endTime ?? '23:59';
                
        
            }

            // Create Request
            $serviceRequest = ServiceRequest::create([
                'hsr_student_service_id' => $studentService->hss_id,
                'hsr_requester_id'       => $user->hu_id,
                'hsr_provider_id'        => $studentService->hss_user_id,
                'hsr_selected_dates'     => [$validated['selected_dates']],
                'hsr_start_time'         => $startTime,
                'hsr_end_time'           => $endTime,
                'hsr_selected_package'   => [$validated['selected_package']],
                'hsr_message'            => $validated['message'] ?? null,
                'hsr_offered_price'      => $validated['offered_price'] ?? null,
                'hsr_status'             => 'pending'
            ]);

            // Notify Provider + Email (non-blocking)
            try {
                $studentService->user->notify(new NewServiceRequest($serviceRequest));

                if ($studentService->user->hu_email) {
                    Mail::to($studentService->user->hu_email)
                        ->send(new NewServiceRequestNotification($serviceRequest, 'provider'));
                }

                if ($user->hu_email) {
                    Mail::to($user->hu_email)
                        ->send(new NewServiceRequestNotification($serviceRequest, 'student'));
                }
            } catch (\Throwable $notifyError) {
                Log::warning('ServiceRequest notifications failed: ' . $notifyError->getMessage(), [
                    'service_request_id' => $serviceRequest->hsr_id,
                    'requester_id' => $user->hu_id,
                    'provider_id' => $studentService->hss_user_id,
                ]);
            }
            

            return response()->json([
                'success' => true,
                'message' => 'Service request sent successfully!',
                'request_id' => $serviceRequest->hsr_id
            ]);

        } catch (\Exception $e) {
            Log::error('ServiceRequest store error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.',
            ], 500);
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
    if ($user->hu_role === 'helper' && $viewMode === 'seller') {
        
        // Fetch only THIS seller's services for the dropdown
        $myServices = \App\Models\StudentService::where('hss_user_id', $user->hu_id)
                        ->selectRaw('hss_id as id, hss_title as title')
                        ->get();

        $query = \App\Models\ServiceRequest::where('hsr_provider_id', $user->hu_id)
            ->with(['requester', 'studentService']);

        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('hss_title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('requester', function($subQ) use ($search) {
                    $subQ->where('hu_name', 'LIKE', "%{$search}%");
                });
            });
        }

        // B. Category Filter
        if ($categoryId) {
            $query->whereHas('studentService', function($q) use ($categoryId) {
                $q->where('hss_category_id', $categoryId);
            });
        }

        // C. Service Type Filter
        if ($selectedServiceId) {
            $query->where('hsr_student_service_id', $selectedServiceId);
        }

        // D. Status Filter (NEW)
        if ($status) {
            $query->where('hsr_status', $status);
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
        $allServiceTypes = \App\Models\StudentService::selectRaw('hss_id as id, hss_title as title')->get();

        $query = \App\Models\ServiceRequest::where('hsr_requester_id', $user->hu_id)
            ->with(['provider', 'studentService']);

        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('hss_title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('provider', function($subQ) use ($search) {
                    $subQ->where('hu_name', 'LIKE', "%{$search}%");
                });
            });
        }

        // B. Category Filter
        if ($categoryId) {
            $query->whereHas('studentService', function($q) use ($categoryId) {
                $q->where('hss_category_id', $categoryId);
            });
        }

        // C. Service Type Filter
        if ($selectedServiceId) {
            $query->where('hsr_student_service_id', $selectedServiceId);
        }

        // D. Status Filter (NEW)
        if ($status) {
            $query->where('hsr_status', $status);
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
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
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
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to accept this request.');
        }

        if (!$serviceRequest->isPending()) {
            return back()->with('error', 'This request cannot be accepted.');
        }

        $serviceRequest->accept();

        // Notify Requester
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'accepted'));

        return back()->with('success', 'Service request accepted successfully!');
    }

    /**
     * Reject a service request
     */
    public function reject(Request $request, ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Authorization check
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to reject this request.');
        }

        if (!$serviceRequest->isPending()) {
            return response()->json([
                'success' => false,
                'message' => 'This request cannot be rejected.',
                'error' => 'This request cannot be rejected.',
            ], 400);
        }

        // 1. VALIDATE: Reason is mandatory
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // 2. UPDATE: Save status AND reason
        $serviceRequest->update([
            'hsr_status' => 'rejected',
            'hsr_rejection_reason' => $request->rejection_reason
        ]);

        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'rejected'));

        return back()->with('success', 'Service request rejected.');
    }

    public function markInProgress(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as in progress
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isAccepted()) {
            return back()->with('error', 'This request must be accepted first.');
        }

        
        $serviceRequest->update([
            'hsr_status' => 'in_progress',
            'hsr_started_at' => now(), 
        ]);
       
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'in_progress'));

        return back()->with('success', 'Service marked as in progress!');
    }

    public function markWorkFinished(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as in progress
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isInProgress()) {
            return back()->with('error', 'This request must be in progress first.');
        }

        
        $serviceRequest->update([
            'hsr_status' => 'waiting_payment',
            'hsr_finished_at' => now(), 
        ]);
       
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'waiting_payment'));

        return back()->with('success', 'Service marked as finished!');
    }

    // BUYER/REQUESTER SIDE TO MAKE PAKMENT
   public function buyerConfirmPayment(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
	$user = Auth::user();
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403);
        }

        // 2. Validate (File is optional, but if present must be an image/pdf)
        $request->validate([
            'payment_proof' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // Max 2MB
        ]);

        // 3. Handle File Upload
        if ($request->hasFile('payment_proof')) {
            $file = $request->file('payment_proof');

            try {
                $extension = strtolower($file->getClientOriginalExtension() ?: $file->extension() ?: 'bin');
                $random = bin2hex(random_bytes(6));
                $filename = 'payment_' . $serviceRequest->hsr_id . '_' . now()->format('YmdHis') . '_' . $random . '.' . $extension;

                $disk = Storage::disk('public');
                $directory = 'payment_proofs';
                $path = $directory . '/' . $filename;

                if (!$disk->exists($directory)) {
                    $disk->makeDirectory($directory);
                }

                $tmpPath = $file->getRealPath() ?: $file->getPathname();
                if (empty($tmpPath) || !is_file($tmpPath)) {
                    return back()->withErrors([
                        'payment_proof' => 'Upload failed. Temporary upload file is missing. Please retry.',
                    ]);
                }

                $contents = @file_get_contents($tmpPath);
                if ($contents === false) {
                    return back()->withErrors([
                        'payment_proof' => 'Upload failed while reading the file. Please retry.',
                    ]);
                }

                $stored = $disk->put($path, $contents);
                if (!$stored || !$disk->exists($path)) {
                    return back()->withErrors(['payment_proof' => 'Payment proof upload failed. Please try again.']);
                }

                $serviceRequest->update(['hsr_payment_proof' => $path]);
            } catch (\Throwable $e) {
                Log::error('Payment proof upload failed', [
                    'service_request_id' => $serviceRequest->hsr_id,
                    'user_id' => $user->hu_id,
                    'message' => $e->getMessage(),
                ]);

                return back()->withErrors([
                    'payment_proof' => 'Upload failed on this environment. Please retry or check server upload temp settings.',
                ]);
            }
        }

        // 4. Update Status
        $serviceRequest->update([
            'hsr_payment_status' => 'verification_status'
        ]);

        return back()->with('success', 'Payment confirmed! Waiting for seller verification.');
    }

    public function finalizeOrder(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
	$user = Auth::user();
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'Unauthorized action.');
        }

        $outcome = $request->input('outcome'); // 'paid' or 'unpaid_problem'

        if ($outcome === 'paid') {
            // ✅ SCENARIO A: Success
            // This updates BOTH status columns at once
            $serviceRequest->update([
                'hsr_status' => 'completed',          // Moves order to History
                'hsr_payment_status' => 'paid',       // Marks payment as Green/Paid
                'hsr_completed_at' => now(),          // Record completion time
            ]);

            // Notify Buyer
            $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

            return back()->with('success', 'Payment confirmed and Order marked as Completed!');
        } 
        
        else {
            $serviceRequest->update([
                'hsr_status' => 'waiting_payment',          // We still close the order
                'hsr_payment_status' => 'unpaid', // But flag it as a problem
                'hsr_completed_at' => now(),
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
            'hsr_status' => 'disputed',
            'hsr_dispute_reason' => $reason,
            'hsr_reported_by' => Auth::id()
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
            'hsr_status' => 'disputed', 
            'hsr_payment_status' => 'dispute', 
            'hsr_dispute_reason' => $request->reason,
            'hsr_reported_by' => Auth::id()
        ]);

        $buyerId = $serviceRequest->hsr_requester_id;

        if ($buyerId) {
            \App\Models\User::where('hu_id', $buyerId)->increment('hu_reports_count');
        }

        return back()->with('success', 'Report submitted. Buyer has been flagged.');
    }

    public function cancelDispute($id)
    {
        $request = ServiceRequest::findOrFail($id);

        // Optional: Security check to ensure only the creator of the dispute or admin can do this
        // if (Auth::id() !== $request->user_id) { abort(403); }

        if ($request->hsr_status === 'disputed') {
            $request->hsr_status = 'completed'; // Set directly to completed as requested
            $request->save();
            
            return back()->with('success', 'Report cancelled. Order marked as completed.');
        }

        return back()->with('error', 'Cannot cancel report at this stage.');
    }

    public function markCompleted(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();
        
        // Only the provider can mark as completed
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isInProgress()) {
            return back()->with('error', 'This request must be in progress first.');
        }

        // --- UBAH KAT SINI ---
        $serviceRequest->update([
            'hsr_status' => 'completed',
            'hsr_completed_at' => now(), // Rekod masa tamat kerja
        ]);

        // Notify Requester
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

        return back()->with('success', 'Service marked as completed! Both parties can now leave reviews.');
    }

        public function markAsPaid($id) {
        $request = ServiceRequest::findOrFail($id);
        $request->update(['hsr_payment_status' => 'paid']);
        return back()->with('success', 'Payment status updated.');
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // 1. Authorization
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to cancel this request.');
        }

        // 2. Block if Completed
        if ($serviceRequest->hsr_status === 'completed') {
            return back()->with('error', 'Completed requests cannot be cancelled.');
        }

        // 3. Block if In Progress (Seller started work)
        if ($serviceRequest->hsr_status === 'in_progress') {
            return back()->with('error', 'The seller has already started working on this request. Please contact the seller directly to discuss cancellation.');
        }

        // 4. Allow Cancellation (for 'pending' or 'accepted')
        $serviceRequest->update(['hsr_status' => 'cancelled']);

        return back()->with('success', 'Service request cancelled successfully.');
    }
        public function updateStatus(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        
        // Validate that the user owns the request or is the provider
        if (Auth::id() !== $serviceRequest->hsr_requester_id && Auth::id() !== $serviceRequest->hsr_provider_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
                'error' => 'Unauthorized',
            ], 403);
        }

        // Validate the new status
        $validated = $request->validate([
            'status' => 'required|in:pending,accepted,rejected,in_progress,waiting_payment,completed,cancelled,disputed,approved'
        ]);

        // Update the status
        $serviceRequest->update([
            'hsr_status' => $validated['status']
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
        if ($serviceRequest->hsr_provider_id !== Auth::id()) {
            abort(403);
        }

        // prevent duplicate review
        if ($serviceRequest->reviewByHelper) {
            return back()->with('error', 'You already reviewed this client.');
        }

        Review::create([
            'hr_service_request_id' => $serviceRequest->hsr_id,
            'hr_reviewer_id' => Auth::id(),
            'hr_reviewee_id' => $serviceRequest->hsr_requester_id,
            'hr_rating' => $request->rating,
            'hr_comment' => $request->comment,
        ]);

        return back()->with('success', 'Review submitted!');
    }

}