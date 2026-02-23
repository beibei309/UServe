<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ServiceApplication;
use App\Models\User;
use App\Models\ServiceApplicationInterest;
use App\Models\ChatRequest;
use App\Models\ServiceRequest;

class ServiceApplicationController extends Controller
{
    /**
     * Show the edit form for a custom application owned by the community user
     */
    public function edit(ServiceApplication $application)
    {
        $user = Auth::user();

        // Only community users can edit their own custom applications
        if ($user->role !== 'community' || $application->user_id !== $user->id) {
            abort(403, 'You are not authorized to edit this application.');
        }

        // Only allow editing custom applications (not linked to a specific student service)
        if (!is_null($application->service_id)) {
            abort(403, 'This application is linked to a service and cannot be edited here.');
        }

        // Allow editing only when application is still open
        if (!in_array($application->status, ['open'])) {
            return redirect()->route('services.applications.show', $application)
                ->with('error', 'You can only edit applications that are still open.');
        }

        return view('services.applications.edit', compact('application'));
    }

    /**
     * Update a custom application owned by the community user
     */
    public function update(Request $request, ServiceApplication $application)
    {
        $user = Auth::user();

        // Authorization checks
        if ($user->role !== 'community' || $application->user_id !== $user->id) {
            abort(403, 'You are not authorized to update this application.');
        }

        if (!is_null($application->service_id)) {
            abort(403, 'This application is linked to a service and cannot be edited here.');
        }

        if (!in_array($application->status, ['open'])) {
            return redirect()->route('services.applications.show', $application)
                ->with('error', 'You can only update applications that are still open.');
        }

        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget_range' => 'nullable|string|max:255',
            'timeline' => 'required|string|max:255',
            'contact_methods' => 'nullable|array',
            'contact_methods.*' => 'string|in:platform_chat,email,phone'
        ]);

        // Store contact methods as array (model casts to JSON)
        $validated['contact_methods'] = $request->contact_methods ?? [];

        $application->update($validated);

        return redirect()->route('services.applications.show', $application)
            ->with('success', 'Application details updated successfully.');
    }
    /**
     * Mark service as completed by current user
     */
    public function markCompleted(ServiceApplication $serviceApplication)
    {
        $user = auth()->user();
        
        // Check if user can mark this application as completed
        if (!$serviceApplication->canBeMarkedCompletedBy($user)) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot mark this service as completed.'
            ], 403);
        }

        try {
            // Mark as completed by the appropriate party
            if ($user->id === $serviceApplication->user_id) {
                $serviceApplication->markCompletedByCustomer();
                $message = 'You have marked this service as completed. Waiting for provider confirmation.';
            } else {
                $serviceApplication->markCompletedByProvider();
                $message = 'You have marked this service as completed. Waiting for customer confirmation.';
            }

            // Check if both parties have completed
            $serviceApplication->refresh();
            if ($serviceApplication->isFullyCompleted()) {
                $message = 'Service has been marked as completed by both parties! You can now leave a review.';
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'fully_completed' => $serviceApplication->isFullyCompleted()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while marking the service as completed.'
            ], 500);
        }
    }

    /**
     * Show the service application form
     */
    public function create()
    {
        // Only allow community users to apply for services
        if (Auth::user()->role !== 'community') {
            return redirect()->route('dashboard')->with('error', 'Only community members can apply for services.');
        }

        return view('services.apply');
    }

    /**
     * Store a new service application
     */
    public function store(Request $request)
    {
        // Only allow community users to apply for services
        if (Auth::user()->role !== 'community') {
            return response()->json(['error' => 'Only community members can apply for services.'], 403);
        }

        // Check if user is verified
        if (!Auth::user()->isVerifiedPublic()) {
            return response()->json(['error' => 'Please complete your account verification first.'], 403);
        }

        $validated = $request->validate([
            'service_type' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'budget_range' => 'nullable|string|max:255',
            'timeline' => 'required|string|max:255',
            'contact_methods' => 'nullable|array',
            'contact_methods.*' => 'string|in:platform_chat,email,phone'
        ]);

        // Store contact methods as array (model casts to JSON)
        $validated['contact_methods'] = $request->contact_methods ?? [];
        $validated['user_id'] = Auth::id();
        $validated['status'] = 'open';

        $application = ServiceApplication::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Service application submitted successfully! Students will be notified and can contact you soon.',
            'application_id' => $application->id
        ]);
    }

    /**
     * Show all applications for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        if ($user->role === 'student') {
            // For students: show OPEN community requests (custom applications) available to all students
            $applications = ServiceApplication::whereNull('service_id')
                ->where('status', 'open')
                ->with(['user'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        } else {
            // For community: show their own applications (services they applied for)
            $applications = ServiceApplication::where('user_id', $user->id)
                ->with(['user', 'service.user', 'service.category'])
                ->orderBy('created_at', 'desc')
                ->paginate(10);
        }
                
        return view('services.applications.index', compact('applications'));
    }

    /**
     * Accept an application (for students)
     */
    public function accept(ServiceApplication $application)
    {
        $user = Auth::user();

        // Guard: only service-targeted applications can be accepted
        if (!$application->service) {
            return response()->json([
                'success' => false,
                'message' => 'This open application cannot be accepted directly.'
            ], 403);
        }

        // Check if user is the service provider
        if ($user->id !== $application->service->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to accept this application.'
            ], 403);
        }

        // Check if application is open
        if ($application->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'This application is no longer open.'
            ], 400);
        }

        $application->update(['status' => 'in_progress']);

        return response()->json([
            'success' => true,
            'message' => 'Application accepted! The service is now in progress.'
        ]);
    }

    /**
     * Reject an application (for students)
     */
    public function reject(ServiceApplication $application)
    {
        $user = Auth::user();

        // Guard: only service-targeted applications can be rejected
        if (!$application->service) {
            return response()->json([
                'success' => false,
                'message' => 'This open application cannot be rejected directly.'
            ], 403);
        }

        // Check if user is the service provider
        if ($user->id !== $application->service->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to reject this application.'
            ], 403);
        }

        // Check if application is open
        if ($application->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'This application is no longer open.'
            ], 400);
        }

        $application->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Application declined.'
        ]);
    }

    /**
     * Show a specific application
     */
    public function show(ServiceApplication $application)
    {
        $user = Auth::user();
        
        // Community users can only see their own applications
        if ($user->role === 'community' && $application->user_id !== $user->id) {
            abort(403, 'You can only view your own applications.');
        }

        return view('services.applications.show', compact('application'));
    }

    /**
     * Apply for a service from within a chat conversation
     */
    public function applyFromChat(Request $request)
    {
        $data = $request->validate([
            'conversation_id' => ['required', 'exists:conversations,id'],
            'service_id' => ['required', 'exists:student_services,id'],
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        $conversation = \App\Models\Conversation::findOrFail($data['conversation_id']);
        $service = \App\Models\StudentService::findOrFail($data['service_id']);
        $user = Auth::user();

        // Check if user is the customer in this conversation
        if ($user->id !== $conversation->customer_id) {
            return response()->json(['error' => 'Only the customer can apply for services.'], 403);
        }

        // Check if service belongs to the student in this conversation
        if ($service->user_id !== $conversation->student_id) {
            return response()->json(['error' => 'Service does not belong to the provider in this conversation.'], 400);
        }

        // Check if service is available
        if ($service->status !== 'available') {
            return response()->json(['error' => 'This service is not available.'], 400);
        }

        // Check if there's already a pending application for this service from this user
        $existingApplication = ServiceApplication::where([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'status' => 'pending'
        ])->first();

        if ($existingApplication) {
            return response()->json(['error' => 'You already have a pending application for this service.'], 400);
        }

        $application = ServiceApplication::create([
            'user_id' => $user->id,
            'service_id' => $service->id,
            'conversation_id' => $conversation->id,
            'title' => 'Application for: ' . $service->title,
            'service_type' => $service->category->name ?? 'General',
            'description' => $data['message'] ?? 'Application submitted from chat',
            'budget_range' => $service->price_range ?? 'As discussed',
            'timeline' => 'As discussed',
            'contact_methods' => ['platform_chat'],
            'status' => 'pending',
        ]);

        return response()->json([
            'application' => $application->load(['user', 'service']),
            'success' => true,
            'message' => 'Service application submitted successfully!'
        ], 201);
    }

    /**
     * Accept a service application from chat
     */
    public function acceptFromChat(Request $request, ServiceApplication $application)
    {
        $user = Auth::user();

        // Check if user is the service provider
        if ($user->id !== $application->service->user_id) {
            return response()->json(['error' => 'You are not authorized to accept this application.'], 403);
        }

        // Check if application is still pending
        if ($application->status !== 'pending') {
            return response()->json(['error' => 'This application is no longer pending.'], 400);
        }

        $application->update(['status' => 'accepted']);
        
        // Make service unavailable
        $application->service->update(['status' => 'busy']);

        return response()->json([
            'success' => true,
            'message' => 'Application accepted successfully! Service is now unavailable to others.'
        ]);
    }

    /**
     * Decline a service application from chat
     */
    public function declineFromChat(Request $request, ServiceApplication $application)
    {
        $user = Auth::user();

        // Check if user is the service provider
        if ($user->id !== $application->service->user_id) {
            return response()->json(['error' => 'You are not authorized to decline this application.'], 403);
        }

        // Check if application is still pending
        if ($application->status !== 'pending') {
            return response()->json(['error' => 'This application is no longer pending.'], 400);
        }

        $application->update(['status' => 'declined']);

        return response()->json([
            'success' => true,
            'message' => 'Application declined.'
        ]);
    }

    /**
     * Student expresses interest in an open custom application
     */
    public function expressInterest(ServiceApplication $application, Request $request)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Only students can express interest.'], 403);
        }

        if ($application->service_id) {
            return response()->json(['success' => false, 'message' => 'Interest is only for open community requests.'], 403);
        }

        if ($application->status !== 'open') {
            return response()->json(['success' => false, 'message' => 'This application is not open.'], 400);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $interest = ServiceApplicationInterest::firstOrCreate(
                [
                    'service_application_id' => $application->id,
                    'student_id' => $user->id,
                ],
                [
                    'message' => $validated['message'] ?? null,
                    'status' => 'interested',
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Interest recorded successfully.',
                'interest_id' => $interest->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to record interest.',
            ], 500);
        }
    }

    /**
     * Student withdraws their interest (if not selected yet)
     */
    public function withdrawInterest(ServiceApplication $application)
    {
        $user = Auth::user();

        if ($user->role !== 'student') {
            return response()->json(['success' => false, 'message' => 'Only students can withdraw interest.'], 403);
        }

        $interest = ServiceApplicationInterest::where('service_application_id', $application->id)
            ->where('student_id', $user->id)
            ->first();

        if (!$interest) {
            return response()->json(['success' => false, 'message' => 'Interest not found.'], 404);
        }

        if ($interest->status === 'selected') {
            return response()->json(['success' => false, 'message' => 'Selected interest cannot be withdrawn.'], 400);
        }

        try {
            $interest->delete();
            return response()->json(['success' => true, 'message' => 'Interest withdrawn.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to withdraw interest.'], 500);
        }
    }

    /**
     * Community user selects a candidate; also sends a chat request
     */
    public function selectInterest(Request $request, ServiceApplication $application, ServiceApplicationInterest $interest)
    {
        $user = Auth::user();

        if ($user->role !== 'community' || $application->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to select a candidate for this application.'], 403);
        }

        if ($application->status !== 'open' || $application->service_id) {
            return response()->json(['success' => false, 'message' => 'Candidate selection is only allowed for open custom applications.'], 400);
        }

        if ($interest->service_application_id !== $application->id) {
            return response()->json(['success' => false, 'message' => 'Interest does not belong to this application.'], 400);
        }

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:500'],
        ]);

        try {
            $interest->update([
                'status' => 'selected',
                'selected_at' => now(),
            ]);

            $chatRequest = ChatRequest::create([
                'requester_id' => $application->user_id,
                'recipient_id' => $interest->student_id,
                'status' => 'pending',
                'message' => $validated['message'] ?? 'Hi! You were selected for my request. Shall we chat?',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Candidate selected and chat request sent.',
                'interest_id' => $interest->id,
                'chat_request_id' => $chatRequest->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to select candidate.',
            ], 500);
        }
    }

    /**
     * Community user declines an interested student
     */
    public function declineInterest(ServiceApplication $application, ServiceApplicationInterest $interest)
    {
        $user = Auth::user();

        if ($user->role !== 'community' || $application->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to decline candidates for this application.'], 403);
        }

        if ($interest->service_application_id !== $application->id) {
            return response()->json(['success' => false, 'message' => 'Interest does not belong to this application.'], 400);
        }

        try {
            $interest->update([
                'status' => 'declined',
                'declined_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Candidate declined.',
                'interest_id' => $interest->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to decline candidate.',
            ], 500);
        }
    }

    /**
     * Community confirms selected candidate and creates a ServiceRequest
     */
    public function confirmSelected(ServiceApplication $application)
    {
        $user = Auth::user();

        if ($user->role !== 'community' || $application->user_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'You are not authorized to confirm selection for this application.'], 403);
        }

        if ($application->status !== 'open' || $application->service_id) {
            return response()->json(['success' => false, 'message' => 'Confirmation is only allowed for open custom applications.'], 400);
        }

        $selected = $application->selectedInterest;
        if (!$selected || $selected->status !== 'selected') {
            return response()->json(['success' => false, 'message' => 'No selected candidate to confirm.'], 400);
        }

        try {
            $serviceRequest = ServiceRequest::create([
                'student_service_id' => null,
                'requester_id' => $application->user_id,
                'provider_id' => $selected->student_id,
                'message' => $application->description ?? 'Custom service request created from application #' . $application->id,
                'status' => 'pending',
            ]);

            $application->update([
                'status' => 'accepted',
            ]);

            return response()->json([
                'success' => true,
                'redirect' => route('service-requests.show', $serviceRequest),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create service request.',
            ], 500);
        }
    }
}