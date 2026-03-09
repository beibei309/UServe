<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\ServiceRequest;
use App\Models\StudentService;
<<<<<<< HEAD
<<<<<<< HEAD
use App\Models\Review;
use App\Models\Cat;
use App\Http\Controllers\PointsController;
=======
=======
use App\Models\Cat;
use App\Http\Controllers\PointsController;
>>>>>>> develop
use App\Notifications\ServiceRequestStatusUpdated;
use App\Services\ServiceImageUrlResolver;
use App\Services\ServiceRequestNotificationService;
use Carbon\Carbon;
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
<<<<<<< HEAD
<<<<<<< HEAD
use Illuminate\Routing\Controller as BaseController;
use App\Notifications\NewServiceRequest;
use App\Notifications\ServiceRequestStatusUpdated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\NewServiceRequestNotification;
use Carbon\Carbon;

=======
use Illuminate\Support\Str;
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
use Illuminate\Support\Str;
>>>>>>> develop

class ServiceRequestController extends BaseController
{
    public function __construct(
        private readonly ServiceRequestNotificationService $serviceRequestNotificationService,
        private readonly ServiceImageUrlResolver $serviceImageUrlResolver,
    ) {
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
<<<<<<< HEAD
<<<<<<< HEAD
                'selected_dates'     => 'required|date',
                'start_time'         => 'nullable|string', 
                'end_time'           => 'nullable|string',
                'selected_package'   => 'required|string',
                'message'            => 'nullable|string|max:1000',
                'offered_price'      => 'nullable|numeric|min:0|max:99999.99'
=======
=======
>>>>>>> develop
                'selected_dates' => 'required|date',
                'start_time' => 'nullable|string',
                'end_time' => 'nullable|string',
                'selected_package' => 'required|string',
                'message' => 'nullable|string|max:1000',
                'offered_price' => 'nullable|numeric|min:0|max:99999.99',
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop
            ]);

            $studentService = StudentService::findOrFail($validated['student_service_id']);

            // Check availability
<<<<<<< HEAD
<<<<<<< HEAD
            if (!$studentService->hss_is_active || !$studentService->user->hu_is_available) {
=======
            if (! $studentService->hss_is_active || ! $studentService->user->hu_is_available) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            if (! $studentService->hss_is_active || ! $studentService->user->hu_is_available) {
>>>>>>> develop
                return response()->json([
                    'success' => false,
                    'message' => 'Service or provider unavailable.',
                ], 400);
            }

            // Check for existing active requests from this user
<<<<<<< HEAD
<<<<<<< HEAD
         $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $user->hu_id)
             ->where('hsr_student_service_id', $studentService->hss_id) // <--- CHANGED: Check specific service ID
             ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
=======
            $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $user->hu_id)
                ->where('hsr_student_service_id', $studentService->hss_id) // <--- CHANGED: Check specific service ID
                ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            $hasActiveRequest = ServiceRequest::where('hsr_requester_id', $user->hu_id)
                ->where('hsr_student_service_id', $studentService->hss_id) // <--- CHANGED: Check specific service ID
                ->whereIn('hsr_status', ['pending', 'accepted', 'in_progress'])
>>>>>>> develop
                ->exists();

            if ($hasActiveRequest) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active request with this helper.',
                ], 400);
            }

            // --- LOGIC SPLIT: Session vs Task ---
            $startTime = $validated['start_time'];
            $endTime = $validated['end_time'];

            // If it is Session Based, we MUST have times and check overlap
            if ($studentService->hss_session_duration) {
<<<<<<< HEAD
<<<<<<< HEAD
                if (!$startTime || !$endTime) {
=======
                if (! $startTime || ! $endTime) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                if (! $startTime || ! $endTime) {
>>>>>>> develop
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
                $endTime = $endTime ?? '23:59';

            }

            // Create Request
            $serviceRequest = ServiceRequest::create([
                'hsr_student_service_id' => $studentService->hss_id,
<<<<<<< HEAD
<<<<<<< HEAD
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
            
=======
                'hsr_requester_id' => $user->hu_id,
                'hsr_provider_id' => $studentService->hss_user_id,
                'hsr_selected_dates' => [$validated['selected_dates']],
                'hsr_start_time' => $startTime,
                'hsr_end_time' => $endTime,
                'hsr_selected_package' => [$validated['selected_package']],
                'hsr_message' => $validated['message'] ?? null,
                'hsr_offered_price' => $validated['offered_price'] ?? null,
                'hsr_status' => 'pending',
            ]);

            $this->serviceRequestNotificationService->notifyCreated($serviceRequest, $studentService, $user);
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                'hsr_requester_id' => $user->hu_id,
                'hsr_provider_id' => $studentService->hss_user_id,
                'hsr_selected_dates' => [$validated['selected_dates']],
                'hsr_start_time' => $startTime,
                'hsr_end_time' => $endTime,
                'hsr_selected_package' => [$validated['selected_package']],
                'hsr_message' => $validated['message'] ?? null,
                'hsr_offered_price' => $validated['offered_price'] ?? null,
                'hsr_status' => 'pending',
            ]);

            $this->serviceRequestNotificationService->notifyCreated($serviceRequest, $studentService, $user);
>>>>>>> develop

            return response()->json([
                'success' => true,
                'message' => 'Service request sent successfully!',
<<<<<<< HEAD
<<<<<<< HEAD
                'request_id' => $serviceRequest->hsr_id
            ]);

        } catch (\Exception $e) {
            Log::error('ServiceRequest store error: ' . $e->getMessage());
=======
                'request_id' => $serviceRequest->hsr_id,
            ]);

        } catch (\Exception $e) {
            Log::error('ServiceRequest store error: '.$e->getMessage());

>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                'request_id' => $serviceRequest->hsr_id,
            ]);

        } catch (\Exception $e) {
            Log::error('ServiceRequest store error: '.$e->getMessage());

>>>>>>> develop
            return response()->json([
                'success' => false,
                'message' => 'Server error occurred.',
            ], 500);
        }
    }

<<<<<<< HEAD
<<<<<<< HEAD
public function index(Request $request)
{
    $user = Auth::user();
    $defaultStatusTab = $request->input('tab', 'pending');
    
    // --- 1. DATA FOR DROPDOWNS ---
    $categories = \App\Models\Category::all();
    
    // --- 2. CAPTURE INPUTS ---
    $search = $request->input('search');
    $categoryId = $request->input('category');
    $selectedServiceId = $request->input('service_type'); 
    $status = $request->input('status'); // NEW: Capture Status
=======
=======
>>>>>>> develop
    public function index(Request $request)
    {
        $user = Auth::user();
        $defaultStatusTab = $request->input('tab', 'pending');
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop

        // --- 1. DATA FOR DROPDOWNS ---
        $categories = \App\Models\Category::all();

        // --- 2. CAPTURE INPUTS ---
        $search = $request->input('search');
        $categoryId = $request->input('category');
        $selectedServiceId = $request->input('service_type');
        $status = $request->input('status'); // NEW: Capture Status

<<<<<<< HEAD
<<<<<<< HEAD
    // ==========================================
    // 3. HELPER MODE (Seller View)
    // ==========================================
    if ($user->hu_role === 'helper' && $viewMode === 'seller') {
        
        // Fetch only THIS seller's services for the dropdown
        $myServices = \App\Models\StudentService::where('hss_user_id', $user->hu_id)
                        ->selectRaw('hss_id as id, hss_title as title')
                        ->get();
=======
        // Safety: Reset service ID if it's invalid
        if (is_array($selectedServiceId) || json_decode((string) $selectedServiceId)) {
            $selectedServiceId = null;
        }
>>>>>>> develop

        $viewMode = session('view_mode', 'buyer');

<<<<<<< HEAD
        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('hss_title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('requester', function($subQ) use ($search) {
                    $subQ->where('hu_name', 'LIKE', "%{$search}%");
=======
        // Safety: Reset service ID if it's invalid
        if (is_array($selectedServiceId) || json_decode((string) $selectedServiceId)) {
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
                ->with(['requester', 'provider', 'studentService.category', 'reviewForHelper', 'reviewByHelper']);

            // A. Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('studentService', function ($subQ) use ($search) {
                        $subQ->where('hss_title', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('requester', function ($subQ) use ($search) {
                            $subQ->where('hu_name', 'LIKE', "%{$search}%");
                        });
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
                });
            }

<<<<<<< HEAD
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
        $this->decorateRequestsForUi($receivedRequests, $user->hu_id, true);

        return view('service-requests.helper', [
            'receivedRequests' => $receivedRequests,
            'categories' => $categories,
            'serviceTypes' => $myServices,
            'defaultStatusTab' => $defaultStatusTab,
        ]);
    }

    // ==========================================
    // 4. BUYER MODE (Student View)
    // ==========================================
    else {
        // Buyers see all services in the dropdown
        $allServiceTypes = \App\Models\StudentService::selectRaw('hss_id as id, hss_title as title')->get();

        $query = \App\Models\ServiceRequest::where('hsr_requester_id', $user->hu_id)
            ->with(['requester', 'provider', 'studentService.category']);

        // A. Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('studentService', function($subQ) use ($search) {
                    $subQ->where('hss_title', 'LIKE', "%{$search}%");
                })
                ->orWhereHas('provider', function($subQ) use ($search) {
                    $subQ->where('hu_name', 'LIKE', "%{$search}%");
=======
=======
        // ==========================================
        // 3. HELPER MODE (Seller View)
        // ==========================================
        if ($user->hu_role === 'helper' && $viewMode === 'seller') {

            // Fetch only THIS seller's services for the dropdown
            $myServices = \App\Models\StudentService::where('hss_user_id', $user->hu_id)
                ->selectRaw('hss_id as id, hss_title as title')
                ->get();

            $query = \App\Models\ServiceRequest::where('hsr_provider_id', $user->hu_id)
                ->with(['requester', 'provider', 'studentService.category', 'reviewForHelper', 'reviewByHelper']);

            // A. Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('studentService', function ($subQ) use ($search) {
                        $subQ->where('hss_title', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('requester', function ($subQ) use ($search) {
                            $subQ->where('hu_name', 'LIKE', "%{$search}%");
                        });
                });
            }

>>>>>>> develop
            // B. Category Filter
            if ($categoryId) {
                $query->whereHas('studentService', function ($q) use ($categoryId) {
                    $q->where('hss_category_id', $categoryId);
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop
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
            $this->decorateRequestsForUi($receivedRequests, $user->hu_id, true);

            return view('service-requests.helper', [
                'receivedRequests' => $receivedRequests,
                'categories' => $categories,
                'serviceTypes' => $myServices,
                'defaultStatusTab' => $defaultStatusTab,
            ]);
        }

<<<<<<< HEAD
<<<<<<< HEAD
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
        $this->decorateRequestsForUi($sentRequests, $user->hu_id, false);
        $uniqueCategories = $sentRequests
            ->map(function (ServiceRequest $serviceRequest) {
                return optional(optional($serviceRequest->studentService)->category)->hc_name ?? 'Other';
            })
            ->unique()
            ->sort()
            ->values();

        return view('service-requests.index', [
            'sentRequests' => $sentRequests,
            'categories' => $categories,
            'serviceTypes' => $allServiceTypes,
            'uniqueCategories' => $uniqueCategories,
            'defaultStatusTab' => $defaultStatusTab,
        ]);
=======
        // ==========================================
        // 4. BUYER MODE (Student View)
        // ==========================================
        else {
            // Buyers see all services in the dropdown
            $allServiceTypes = \App\Models\StudentService::selectRaw('hss_id as id, hss_title as title')->get();

            $query = \App\Models\ServiceRequest::where('hsr_requester_id', $user->hu_id)
                ->with(['requester', 'provider', 'studentService.category']);

            // A. Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('studentService', function ($subQ) use ($search) {
                        $subQ->where('hss_title', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('provider', function ($subQ) use ($search) {
                            $subQ->where('hu_name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // B. Category Filter
            if ($categoryId) {
                $query->whereHas('studentService', function ($q) use ($categoryId) {
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
            $this->decorateRequestsForUi($sentRequests, $user->hu_id, false);
            $uniqueCategories = $sentRequests
                ->map(function (ServiceRequest $serviceRequest) {
                    return optional(optional($serviceRequest->studentService)->category)->hc_name ?? 'Other';
                })
                ->unique()
                ->sort()
                ->values();

            return view('service-requests.index', [
                'sentRequests' => $sentRequests,
                'categories' => $categories,
                'serviceTypes' => $allServiceTypes,
                'uniqueCategories' => $uniqueCategories,
                'defaultStatusTab' => $defaultStatusTab,
            ]);
        }
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        // ==========================================
        // 4. BUYER MODE (Student View)
        // ==========================================
        else {
            // Buyers see all services in the dropdown
            $allServiceTypes = \App\Models\StudentService::selectRaw('hss_id as id, hss_title as title')->get();

            $query = \App\Models\ServiceRequest::where('hsr_requester_id', $user->hu_id)
                ->with(['requester', 'provider', 'studentService.category']);

            // A. Search
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('studentService', function ($subQ) use ($search) {
                        $subQ->where('hss_title', 'LIKE', "%{$search}%");
                    })
                        ->orWhereHas('provider', function ($subQ) use ($search) {
                            $subQ->where('hu_name', 'LIKE', "%{$search}%");
                        });
                });
            }

            // B. Category Filter
            if ($categoryId) {
                $query->whereHas('studentService', function ($q) use ($categoryId) {
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
            $this->decorateRequestsForUi($sentRequests, $user->hu_id, false);
            $uniqueCategories = $sentRequests
                ->map(function (ServiceRequest $serviceRequest) {
                    return optional(optional($serviceRequest->studentService)->category)->hc_name ?? 'Other';
                })
                ->unique()
                ->sort()
                ->values();

            return view('service-requests.index', [
                'sentRequests' => $sentRequests,
                'categories' => $categories,
                'serviceTypes' => $allServiceTypes,
                'uniqueCategories' => $uniqueCategories,
                'defaultStatusTab' => $defaultStatusTab,
            ]);
        }
>>>>>>> develop
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
            'reviews.reviewer',
        ]);
        $service = $serviceRequest->studentService;

        $currentUserId = (int) ($user->hu_id ?? $user->id);
        $isRequester = $currentUserId === (int) $serviceRequest->hsr_requester_id;
        $isProvider = $currentUserId === (int) $serviceRequest->hsr_provider_id;
        $providerRestricted = (bool) ($serviceRequest->provider->hu_is_suspended || $serviceRequest->provider->hu_is_blacklisted || $serviceRequest->provider->hu_is_blocked);
        $buyerRestricted = (bool) ($serviceRequest->requester->hu_is_suspended || $serviceRequest->requester->hu_is_blacklisted);
        $isRestricted = $providerRestricted || $buyerRestricted;

        $selectedPackageRaw = is_array($serviceRequest->hsr_selected_package)
            ? ($serviceRequest->hsr_selected_package[0] ?? 'custom')
            : ($serviceRequest->hsr_selected_package ?? 'custom');
        $selectedPackageLabel = trim(str_replace('"', '', (string) $selectedPackageRaw));
        $selectedPackageLabel = $selectedPackageLabel !== '' ? $selectedPackageLabel : 'Custom';

        $headerVisual = $this->resolveShowHeaderVisual($serviceRequest->hsr_status, $isRestricted);

        $requestedDateDisplays = collect((array) $serviceRequest->hsr_selected_dates)
            ->filter()
            ->map(function ($date) {
                try {
                    return Carbon::parse($date)->format('l, F j, Y');
                } catch (\Throwable $e) {
                    return (string) $date;
                }
            })
            ->values();

        $providerAverageRating = Review::where('hr_reviewee_id', $serviceRequest->provider->hu_id)->avg('hr_rating') ?? 0;
        $buyerAverageRating = Review::where('hr_reviewee_id', $serviceRequest->requester->hu_id)->avg('hr_rating') ?? 0;
        $hasCurrentUserReviewed = $serviceRequest->reviews
            ->contains(fn (Review $review) => (int) $review->hr_reviewer_id === $currentUserId);
        $contactPhone = $isProvider ? $serviceRequest->requester->hu_phone : $serviceRequest->provider->hu_phone;

<<<<<<< HEAD
<<<<<<< HEAD
        $serviceImageFallback = 'https://ui-avatars.com/api/?name=' . urlencode($service->hss_title ?? 'Service');
        $serviceImageUrl = null;
        if (!empty($service->hss_image_path)) {
            $path = $service->hss_image_path;
            if (Str::startsWith($path, ['http://', 'https://'])) {
                $serviceImageUrl = $path;
            } elseif (Str::startsWith($path, 'storage/')) {
                $serviceImageUrl = asset($path);
            } elseif (file_exists(public_path('storage/' . $path))) {
                $serviceImageUrl = asset('storage/' . $path);
            } elseif (file_exists(public_path($path))) {
                $serviceImageUrl = asset($path);
            } else {
                $serviceImageUrl = $serviceImageFallback;
            }
        }
=======
=======
>>>>>>> develop
        $serviceImageFallback = 'https://ui-avatars.com/api/?name='.urlencode($service->hss_title ?? 'Service');
        $serviceImageUrl = $this->serviceImageUrlResolver->resolveGeneralImageUrl(
            $service->hss_image_path,
            $serviceImageFallback
        );
<<<<<<< HEAD
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop

        $paymentProofUrl = null;
        $paymentProofIsPdf = false;
        if ($serviceRequest->hsr_payment_proof) {
            $proofPath = $serviceRequest->hsr_payment_proof;
            if (Str::startsWith($proofPath, ['http://', 'https://'])) {
                $paymentProofUrl = $proofPath;
            } elseif (Str::startsWith($proofPath, 'storage/')) {
                $paymentProofUrl = asset($proofPath);
            } else {
<<<<<<< HEAD
<<<<<<< HEAD
                $paymentProofUrl = asset('storage/' . $proofPath);
=======
                $paymentProofUrl = asset('storage/'.$proofPath);
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                $paymentProofUrl = asset('storage/'.$proofPath);
>>>>>>> develop
            }
            $paymentProofIsPdf = Str::endsWith(strtolower($proofPath), '.pdf');
        }

        return view('service-requests.show', compact(
            'serviceRequest',
            'service',
            'isRequester',
            'isProvider',
            'providerRestricted',
            'buyerRestricted',
            'isRestricted',
            'selectedPackageLabel',
            'headerVisual',
            'requestedDateDisplays',
            'providerAverageRating',
            'buyerAverageRating',
            'hasCurrentUserReviewed',
            'contactPhone',
            'serviceImageUrl',
            'serviceImageFallback',
            'paymentProofUrl',
            'paymentProofIsPdf'
        ));
    }

    private function decorateRequestsForUi($requests, int $currentUserId, bool $helperView): void
    {
<<<<<<< HEAD
<<<<<<< HEAD
        $reviewedIds = Review::where('hr_reviewer_id', $currentUserId)
            ->whereIn('hr_service_request_id', $requests->pluck('hsr_id')->all())
            ->pluck('hr_service_request_id')
            ->flip();
=======
        $requestIds = $requests->pluck('hsr_id')->all();
        $reviewsByRequest = collect();
        if (!empty($requestIds)) {
            $reviewsByRequest = Review::whereIn('hr_service_request_id', $requestIds)
                ->get()
                ->groupBy('hr_service_request_id');
        }
>>>>>>> develop

        $requests->transform(function (ServiceRequest $request) use ($reviewsByRequest, $helperView, $currentUserId) {
            $service = $request->studentService;
<<<<<<< HEAD
=======
        $requestIds = $requests->pluck('hsr_id')->all();
        $reviewsByRequest = collect();
        if (!empty($requestIds)) {
            $reviewsByRequest = Review::whereIn('hr_service_request_id', $requestIds)
                ->get()
                ->groupBy('hr_service_request_id');
        }

        $requests->transform(function (ServiceRequest $request) use ($reviewsByRequest, $helperView, $currentUserId) {
            $service = $request->studentService;
            $requestReviews = $reviewsByRequest->get((int) $request->hsr_id, collect());
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            $requestReviews = $reviewsByRequest->get((int) $request->hsr_id, collect());
>>>>>>> develop
            $selectedPackageRaw = is_array($request->hsr_selected_package)
                ? ($request->hsr_selected_package[0] ?? 'custom')
                : ($request->hsr_selected_package ?? 'custom');
            $packageLabel = trim(str_replace('"', '', (string) $selectedPackageRaw));
            $packageLabel = $packageLabel !== '' ? $packageLabel : 'Custom';
            $pkgType = strtolower($packageLabel);

            $dates = is_array($request->hsr_selected_dates) ? $request->hsr_selected_dates : (array) $request->hsr_selected_dates;
            $dates = array_values(array_filter($dates));
            $firstDate = $dates[0] ?? null;
            $firstDateDisplay = null;
            if ($firstDate) {
                try {
                    $firstDateDisplay = Carbon::parse($firstDate)->format('M j, Y');
                } catch (\Throwable $e) {
                    $firstDateDisplay = (string) $firstDate;
                }
            }

            $isSellerRestricted = (bool) ($request->provider->hu_is_suspended || $request->provider->hu_is_blacklisted || $request->provider->hu_is_blocked);
            $hasDatePassed = false;
            if ($firstDate) {
                try {
                    $hasDatePassed = now()->startOfDay()->gte(Carbon::parse($firstDate)->startOfDay());
                } catch (\Throwable $e) {
                    $hasDatePassed = false;
                }
            }

            $request->ui_package_label = $packageLabel;
<<<<<<< HEAD
<<<<<<< HEAD
            $request->ui_pkg_duration = $service->{$pkgType . '_duration'} ?? null;
            $request->ui_pkg_frequency = $service->{$pkgType . '_frequency'} ?? null;
=======
            $request->ui_pkg_duration = $service->{$pkgType.'_duration'} ?? null;
            $request->ui_pkg_frequency = $service->{$pkgType.'_frequency'} ?? null;
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            $request->ui_pkg_duration = $service->{$pkgType.'_duration'} ?? null;
            $request->ui_pkg_frequency = $service->{$pkgType.'_frequency'} ?? null;
>>>>>>> develop
            $request->ui_first_date_display = $firstDateDisplay;
            $request->ui_date_count = count($dates);
            $request->ui_is_seller_restricted = $isSellerRestricted;
            $request->ui_has_date_passed = $hasDatePassed;
            $request->ui_created_human = optional($request->created_at)->diffForHumans();
            $request->ui_updated_human = optional($request->updated_at)->diffForHumans();
            $request->ui_updated_date = optional($request->updated_at)->format('M j, Y');
<<<<<<< HEAD
<<<<<<< HEAD
            $request->ui_reviewed_by_auth = isset($reviewedIds[$request->hsr_id]);
=======
            $request->ui_reviewed_by_auth = $requestReviews->contains(function (Review $review) use ($currentUserId) {
                return (int) $review->hr_reviewer_id === $currentUserId;
            });
            $request->ui_review_for_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewee_id === (int) $request->hsr_provider_id;
            });
            $request->ui_review_by_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewer_id === (int) $request->hsr_provider_id;
            });
>>>>>>> develop
            $request->ui_display_id = str_pad((string) $request->hsr_id, 5, '0', STR_PAD_LEFT);
            $paymentProofPath = (string) ($request->hsr_payment_proof ?? '');
            $request->ui_has_payment_proof = $paymentProofPath !== '';
            $request->ui_payment_proof_url = null;
            if ($request->ui_has_payment_proof) {
                if (Str::startsWith($paymentProofPath, ['http://', 'https://'])) {
                    $request->ui_payment_proof_url = $paymentProofPath;
                } elseif (Str::startsWith($paymentProofPath, 'storage/')) {
                    $request->ui_payment_proof_url = asset($paymentProofPath);
                } else {
                    $request->ui_payment_proof_url = asset('storage/' . ltrim($paymentProofPath, '/'));
                }
            }

<<<<<<< HEAD
            if (!$helperView) {
=======
            $request->ui_reviewed_by_auth = $requestReviews->contains(function (Review $review) use ($currentUserId) {
                return (int) $review->hr_reviewer_id === $currentUserId;
            });
            $request->ui_review_for_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewee_id === (int) $request->hsr_provider_id;
            });
            $request->ui_review_by_helper = $requestReviews->first(function (Review $review) use ($request) {
                return (int) $review->hr_reviewer_id === (int) $request->hsr_provider_id;
            });
            $request->ui_display_id = str_pad((string) $request->hsr_id, 5, '0', STR_PAD_LEFT);
            $paymentProofPath = (string) ($request->hsr_payment_proof ?? '');
            $request->ui_has_payment_proof = $paymentProofPath !== '';
            $request->ui_payment_proof_url = null;
            if ($request->ui_has_payment_proof) {
                if (Str::startsWith($paymentProofPath, ['http://', 'https://'])) {
                    $request->ui_payment_proof_url = $paymentProofPath;
                } elseif (Str::startsWith($paymentProofPath, 'storage/')) {
                    $request->ui_payment_proof_url = asset($paymentProofPath);
                } else {
                    $request->ui_payment_proof_url = asset('storage/' . ltrim($paymentProofPath, '/'));
                }
            }

            if (! $helperView) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            if (! $helperView) {
>>>>>>> develop
                $request->ui_status_text = strtoupper(str_replace('_', ' ', (string) $request->hsr_status));
                $request->ui_inprogress_style = $this->resolveBuyerInProgressStyles($request->hsr_status, $isSellerRestricted);
                $request->ui_completed_theme = $this->resolveBuyerCompletedTheme($request->hsr_status);
            } else {
                $request->ui_helper_inprogress_theme = $this->resolveHelperInProgressTheme($request->hsr_status);
                $request->ui_helper_completed_theme = $this->resolveHelperCompletedTheme($request->hsr_status);
            }

            return $request;
        });
    }

    private function resolveBuyerInProgressStyles(string $status, bool $isSellerRestricted): array
    {
        if ($isSellerRestricted) {
            return [
                'badge' => 'border-red-200 bg-red-100 text-red-700',
                'card' => 'border-red-300 bg-red-50 hover:border-red-400',
                'stripe' => 'bg-red-500',
                'status_text' => 'SELLER RESTRICTED',
            ];
        }

        $badge = match ($status) {
            'disputed' => 'border-red-200 bg-red-50 text-red-700',
            'waiting_payment' => 'border-yellow-200 bg-yellow-50 text-yellow-700',
            default => 'border-blue-200 bg-blue-50 text-blue-700',
        };
        $card = match ($status) {
            'disputed' => 'border-red-200 hover:border-red-300 bg-white',
            'waiting_payment' => 'border-yellow-200 hover:border-yellow-300 bg-white',
            default => 'border-blue-100 hover:border-blue-200 bg-white',
        };
        $stripe = match ($status) {
            'disputed' => 'bg-red-500',
            'waiting_payment' => 'bg-yellow-500',
            default => 'bg-blue-500',
        };

        return [
            'badge' => $badge,
            'card' => $card,
            'stripe' => $stripe,
            'status_text' => strtoupper(str_replace('_', ' ', $status)),
        ];
    }

    private function resolveBuyerCompletedTheme(string $status): array
    {
        return match ($status) {
            'completed' => [
                'border' => 'border-green-200 hover:border-green-300',
                'strip' => 'bg-green-500',
                'badge' => 'bg-green-100 text-green-700',
                'icon_bg' => 'bg-green-50',
                'icon_text' => 'text-green-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            'rejected' => [
                'border' => 'border-red-200 hover:border-red-300',
                'strip' => 'bg-red-500',
                'badge' => 'bg-red-100 text-red-700',
                'icon_bg' => 'bg-red-50',
                'icon_text' => 'text-red-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            default => [
                'border' => 'border-gray-200 hover:border-gray-300',
                'strip' => 'bg-gray-400',
                'badge' => 'bg-gray-100 text-gray-600',
                'icon_bg' => 'bg-gray-50',
                'icon_text' => 'text-gray-500',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
            ],
        };
    }

    private function resolveHelperInProgressTheme(string $status): array
    {
        return match ($status) {
            'disputed' => ['color' => 'red', 'border' => 'border-red-200', 'bg' => 'bg-red-500'],
            'waiting_payment' => ['color' => 'yellow', 'border' => 'border-yellow-200', 'bg' => 'bg-yellow-400'],
            'in_progress' => ['color' => 'blue', 'border' => 'border-blue-200', 'bg' => 'bg-blue-500'],
            default => ['color' => 'gray', 'border' => 'border-gray-200', 'bg' => 'bg-gray-400'],
        };
    }

    private function resolveHelperCompletedTheme(string $status): array
    {
        return match ($status) {
            'completed' => [
                'border' => 'border-green-200 hover:border-green-300',
                'strip' => 'bg-green-500',
                'badge' => 'bg-green-100 text-green-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            'rejected' => [
                'border' => 'border-red-200 hover:border-red-300',
                'strip' => 'bg-red-500',
                'badge' => 'bg-red-100 text-red-700',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
            ],
            default => [
                'border' => 'border-gray-200 hover:border-gray-300',
                'strip' => 'bg-gray-400',
                'badge' => 'bg-gray-100 text-gray-600',
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />',
            ],
        };
    }

    private function resolveShowHeaderVisual(string $status, bool $isRestricted): array
    {
        if ($isRestricted) {
            return ['style' => 'background: linear-gradient(to right, #ef4444, #b91c1c);', 'dot' => 'bg-red-400'];
        }

        return match ($status) {
            'in_progress', 'accepted' => ['style' => 'background: linear-gradient(to right, #3b82f6, #4f46e5);', 'dot' => 'bg-blue-300'],
            'completed' => ['style' => 'background: linear-gradient(to right, #10b981, #059669);', 'dot' => 'bg-green-300'],
            'cancelled', 'rejected', 'disputed' => ['style' => 'background: linear-gradient(to right, #6b7280, #374151);', 'dot' => 'bg-red-300'],
            'waiting_payment' => ['style' => 'background: linear-gradient(to right, #facc15, #ca8a04);', 'dot' => 'bg-yellow-300'],
            default => ['style' => 'background: linear-gradient(to right, #facc15, #fb923c);', 'dot' => 'bg-white'],
        };
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

<<<<<<< HEAD
<<<<<<< HEAD
        if (!$serviceRequest->isPending()) {
=======
        if (! $serviceRequest->isPending()) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if (! $serviceRequest->isPending()) {
>>>>>>> develop
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

<<<<<<< HEAD
<<<<<<< HEAD
        if (!$serviceRequest->isPending()) {
=======
        if (! $serviceRequest->isPending()) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if (! $serviceRequest->isPending()) {
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_rejection_reason' => $request->rejection_reason
=======
            'hsr_rejection_reason' => $request->rejection_reason,
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_rejection_reason' => $request->rejection_reason,
>>>>>>> develop
        ]);

        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'rejected'));

        return back()->with('success', 'Service request rejected.');
    }

    public function markInProgress(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // Only the provider can mark as in progress
<<<<<<< HEAD
<<<<<<< HEAD
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (!$serviceRequest->isAccepted()) {
=======
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (! $serviceRequest->isAccepted()) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
            abort(403, 'You are not authorized to update this request.');
        }

        if (! $serviceRequest->isAccepted()) {
>>>>>>> develop
            return back()->with('error', 'This request must be accepted first.');
        }

        $serviceRequest->update([
            'hsr_status' => 'in_progress',
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_started_at' => now(), 
=======
            'hsr_started_at' => now(),
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_started_at' => now(),
>>>>>>> develop
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

<<<<<<< HEAD
<<<<<<< HEAD
        if (!$serviceRequest->isInProgress()) {
=======
        if (! $serviceRequest->isInProgress()) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if (! $serviceRequest->isInProgress()) {
>>>>>>> develop
            return back()->with('error', 'This request must be in progress first.');
        }

        $serviceRequest->update([
            'hsr_status' => 'waiting_payment',
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_finished_at' => now(), 
=======
            'hsr_finished_at' => now(),
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_finished_at' => now(),
>>>>>>> develop
        ]);

        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'waiting_payment'));

        return back()->with('success', 'Service marked as finished!');
    }

    // BUYER/REQUESTER SIDE TO MAKE PAKMENT
    public function buyerConfirmPayment(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
<<<<<<< HEAD
<<<<<<< HEAD
	$user = Auth::user();
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
=======
        $user = Auth::user();
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        $user = Auth::user();
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
                $filename = 'payment_' . $serviceRequest->hsr_id . '_' . now()->format('YmdHis') . '_' . $random . '.' . $extension;
=======
                $filename = 'payment_'.$serviceRequest->hsr_id.'_'.now()->format('YmdHis').'_'.$random.'.'.$extension;
>>>>>>> develop

                $disk = Storage::disk('public');
                $directory = 'payment_proofs';
                $path = $directory.'/'.$filename;

<<<<<<< HEAD
                if (!$disk->exists($directory)) {
=======
                $filename = 'payment_'.$serviceRequest->hsr_id.'_'.now()->format('YmdHis').'_'.$random.'.'.$extension;

                $disk = Storage::disk('public');
                $directory = 'payment_proofs';
                $path = $directory.'/'.$filename;

                if (! $disk->exists($directory)) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                if (! $disk->exists($directory)) {
>>>>>>> develop
                    $disk->makeDirectory($directory);
                }

                $tmpPath = $file->getRealPath() ?: $file->getPathname();
<<<<<<< HEAD
<<<<<<< HEAD
                if (empty($tmpPath) || !is_file($tmpPath)) {
=======
                if (empty($tmpPath) || ! is_file($tmpPath)) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                if (empty($tmpPath) || ! is_file($tmpPath)) {
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
                if (!$stored || !$disk->exists($path)) {
=======
                if (! $stored || ! $disk->exists($path)) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
                if (! $stored || ! $disk->exists($path)) {
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_payment_status' => 'verification_status'
=======
            'hsr_payment_status' => 'verification_status',
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_payment_status' => 'verification_status',
>>>>>>> develop
        ]);

        return back()->with('success', 'Payment confirmed! Waiting for seller verification.');
    }

    public function finalizeOrder(Request $request, ServiceRequest $serviceRequest)
    {
        // 1. Authorization
<<<<<<< HEAD
<<<<<<< HEAD
	$user = Auth::user();
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
=======
        $user = Auth::user();
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        $user = Auth::user();
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> develop
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

            // Award seller points for completed sale
            PointsController::awardPointsForCompletedService($serviceRequest);

            // Notify Buyer
            $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

<<<<<<< HEAD
            return back()->with('success', 'Payment confirmed and Order marked as Completed! Seller earned 1 point.');
<<<<<<< HEAD
        } 
        
        else {
=======
            return back()->with('success', 'Payment confirmed and Order marked as Completed!');
        } else {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        } else {
>>>>>>> develop
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
        if ($request->additional_notes) {
            $reason .= ' - Note: '.$request->additional_notes;
        }

        // Update status to 'disputed'
        $serviceRequest->update([
            'hsr_status' => 'disputed',
            'hsr_dispute_reason' => $reason,
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_reported_by' => Auth::id()
=======
            'hsr_reported_by' => Auth::id(),
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_reported_by' => Auth::id(),
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_status' => 'disputed', 
            'hsr_payment_status' => 'dispute', 
            'hsr_dispute_reason' => $request->reason,
            'hsr_reported_by' => Auth::id()
=======
            'hsr_status' => 'disputed',
            'hsr_payment_status' => 'dispute',
            'hsr_dispute_reason' => $request->reason,
            'hsr_reported_by' => Auth::id(),
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_status' => 'disputed',
            'hsr_payment_status' => 'dispute',
            'hsr_dispute_reason' => $request->reason,
            'hsr_reported_by' => Auth::id(),
>>>>>>> develop
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
<<<<<<< HEAD
            
            // Award seller points since order is now completed
            PointsController::awardPointsForCompletedService($request);
            
            return back()->with('success', 'Report cancelled. Order marked as completed. Seller earned 1 point.');
=======

            return back()->with('success', 'Report cancelled. Order marked as completed.');
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
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

<<<<<<< HEAD
<<<<<<< HEAD
        if (!$serviceRequest->isInProgress()) {
=======
        if (! $serviceRequest->isInProgress()) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if (! $serviceRequest->isInProgress()) {
>>>>>>> develop
            return back()->with('error', 'This request must be in progress first.');
        }

        // --- UBAH KAT SINI ---
        $serviceRequest->update([
            'hsr_status' => 'completed',
            'hsr_completed_at' => now(), // Rekod masa tamat kerja
        ]);

        // Award seller points for completed service
        PointsController::awardPointsForCompletedService($serviceRequest);

        // Notify Requester
        $serviceRequest->requester->notify(new ServiceRequestStatusUpdated($serviceRequest, 'completed'));

<<<<<<< HEAD
        return back()->with('success', 'Service marked as completed! Both parties can now leave reviews. You earned 1 point!');
=======
        return back()->with('success', 'Service marked as completed! Both parties can now leave reviews.');
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
    }

    public function markAsPaid($id)
    {
        $request = ServiceRequest::findOrFail($id);
        $request->update(['hsr_payment_status' => 'paid']);
<<<<<<< HEAD
<<<<<<< HEAD
=======

>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======

>>>>>>> develop
        return back()->with('success', 'Payment status updated.');
    }

    public function cancel(ServiceRequest $serviceRequest)
    {
        $user = Auth::user();

        // 1. Authorization
<<<<<<< HEAD
<<<<<<< HEAD
       if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
=======
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
        if ($serviceRequest->hsr_requester_id != $user->hu_id && $serviceRequest->hsr_provider_id != $user->hu_id) {
>>>>>>> develop
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
<<<<<<< HEAD
<<<<<<< HEAD
            'status' => 'required|in:pending,accepted,rejected,in_progress,waiting_payment,completed,cancelled,disputed,approved'
=======
            'status' => 'required|in:pending,accepted,rejected,in_progress,waiting_payment,completed,cancelled,disputed,approved',
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'status' => 'required|in:pending,accepted,rejected,in_progress,waiting_payment,completed,cancelled,disputed,approved',
>>>>>>> develop
        ]);

        // Update the status
        $serviceRequest->update([
<<<<<<< HEAD
<<<<<<< HEAD
            'hsr_status' => $validated['status']
=======
            'hsr_status' => $validated['status'],
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
            'hsr_status' => $validated['status'],
>>>>>>> develop
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking status updated to '.$validated['status'],
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
<<<<<<< HEAD
<<<<<<< HEAD

=======
>>>>>>> 00141b2 (fix: stabilize helper request flows and mode switching)
=======
>>>>>>> develop
}
