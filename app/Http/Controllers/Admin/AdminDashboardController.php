<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CertificateRedemption;
use App\Models\Report;
use App\Models\Review;
use App\Models\RewardRedemption;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\StudentService;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
{
    // TOTAL COUNTS
        $totalStudents = User::where('hu_role', 'student')->count();
        $totalHelpers = User::where('hu_role', 'helper')->count();
        $totalStudentAccounts = $totalStudents + $totalHelpers;
        $totalCommunityUsers = User::where('hu_role', 'community')->count();
    $totalServices = StudentService::count();
    $totalRequests = ServiceRequest::count();
    $totalReports = Report::count();
    $totalReviews = Review::count();

    // ===============================
        // 🔔 ADMIN ACTION REQUIRED
        // ===============================

        // Pending student verification
        $pendingStudents = User::where('hu_role', 'student')
            ->where('hu_verification_status', 'pending')
            ->count();

        // Pending helper verification
        $pendingHelpers = User::where('hu_role', 'helper')
            ->where('hu_verification_status', 'pending')
            ->count();

        // Pending services approval
        $pendingServices = StudentService::where('hss_approval_status', 'pending')->count();
        $pendingCommunityVerifications = User::where('hu_role', 'community')
            ->where('hu_verification_status', 'pending')
            ->count();
        $openReports = Report::where('hrp_status', 'open')->count();
        $disputedRequests = ServiceRequest::where('hsr_status', 'disputed')->count();
        $pendingPaymentProofs = ServiceRequest::whereIn('hsr_payment_status', ['pending_verification', 'verification_status'])
            ->count();

        $studentsWithoutStatus = User::whereIn('hu_role', ['student', 'helper'])
    ->whereNotIn('hu_id', function ($query) {
        $query->select('hss_student_id')
              ->from('h2u_student_statuses');
    })
    ->count();

    $serviceStatusCounts = StudentService::select('hss_approval_status', DB::raw('COUNT(*) as total'))
        ->groupBy('hss_approval_status')
        ->pluck('total', 'hss_approval_status')
        ->all();

    $requestStatusCounts = ServiceRequest::select('hsr_status', DB::raw('COUNT(*) as total'))
        ->groupBy('hsr_status')
        ->pluck('total', 'hsr_status')
        ->all();

    $paymentStatusCounts = ServiceRequest::select('hsr_payment_status', DB::raw('COUNT(*) as total'))
        ->groupBy('hsr_payment_status')
        ->pluck('total', 'hsr_payment_status')
        ->all();

    $moderationCounts = [
        'blocked' => User::where('hu_is_blocked', true)->count(),
        'suspended' => User::where('hu_is_suspended', true)->count(),
        'blacklisted' => User::where('hu_is_blacklisted', true)->count(),
        'warned' => User::where('hu_warning_count', '>', 0)->count(),
    ];

    $topCategories = Category::withCount('services')
        ->orderByDesc('services_count')
        ->limit(5)
        ->get(['hc_id', 'hc_name', 'hc_color']);

    $topServices = StudentService::with(['user', 'category'])
        ->withAvg('reviews as reviews_avg_rating', 'hr_rating')
        ->withCount('reviews')
        ->orderByDesc('reviews_count')
        ->orderByDesc('reviews_avg_rating')
        ->limit(5)
        ->get();

    $highWarningServices = StudentService::with(['user'])
        ->where('hss_warning_count', '>', 0)
        ->orderByDesc('hss_warning_count')
        ->limit(5)
        ->get(['hss_id', 'hss_title', 'hss_user_id', 'hss_warning_count', 'hss_approval_status']);

    $recentRequests = ServiceRequest::with(['studentService', 'requester', 'provider'])
        ->latest()
        ->limit(5)
        ->get();

    $rewardSnapshot = [
        'redemptions' => RewardRedemption::count(),
        'pendingRedemptions' => RewardRedemption::where('hrr_status', 'pending')->count(),
        'certificates' => CertificateRedemption::count(),
        'pendingCertificates' => CertificateRedemption::where('hcr_status', 'pending')->count(),
    ];

    /* ---------------------------------------------
     |  MONTHLY STUDENT REGISTRATIONS (Line Chart)
     --------------------------------------------- */
    $studentData = User::where('hu_role', 'student')
        ->selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month');   // returns: [1 => 10, 2 => 14, ...]

    // Fill all 12 months
    $studentsPerMonth = array_fill(1, 12, 0);
    foreach ($studentData as $month => $count) {
        $studentsPerMonth[$month] = $count;
    }

    /* ---------------------------------------------
     |  MONTHLY SERVICES CREATED (Bar Chart)
     --------------------------------------------- */
    $serviceData = StudentService::selectRaw('EXTRACT(MONTH FROM created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $servicesPerMonth = array_fill(1, 12, 0);
    foreach ($serviceData as $month => $count) {
        $servicesPerMonth[$month] = $count;
    }

    return view('admin.dashboard', compact(
        'totalStudents',
        'totalHelpers',
        'totalStudentAccounts',
        'totalCommunityUsers',
        'totalServices',
        'totalRequests',
        'totalReports',
        'totalReviews',
        'pendingStudents',
        'pendingHelpers',  
        'pendingServices',
        'pendingCommunityVerifications',
        'openReports',
        'disputedRequests',
        'pendingPaymentProofs',
        'studentsPerMonth',
        'servicesPerMonth',
        'studentsWithoutStatus',
        'serviceStatusCounts',
        'requestStatusCounts',
        'paymentStatusCounts',
        'moderationCounts',
        'topCategories',
        'topServices',
        'highWarningServices',
        'recentRequests',
        'rewardSnapshot',
    ));
}

}
