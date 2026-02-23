<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentService;
use App\Models\ServiceRequest;
use App\Models\StudentStatus;

class AdminDashboardController extends Controller
{
    public function index()
{
    // TOTAL COUNTS
    $totalStudents = User::where('role', 'student')->count();
    $totalCommunityUsers = User::where('role', 'community')->count();
    $totalServices = StudentService::count();
    $pendingRequests = ServiceRequest::where('status', 'pending')->count();

    // ===============================
        // 🔔 ADMIN ACTION REQUIRED
        // ===============================

        // Pending student verification
        $pendingStudents = User::where('role', 'student')
            ->where('verification_status', 'pending')
            ->count();

        // Pending helper verification
        $pendingHelpers = User::where('role', 'helper')
            ->where('verification_status', 'pending')
            ->count();

        // Pending services approval
        $pendingServices = StudentService::where('status', 'pending')->count();

        $studentsWithoutStatus = User::where('role', 'student')
    ->whereNotIn('id', function ($query) {
        $query->select('student_id')
              ->from('student_statuses');
    })
    ->count();

    /* ---------------------------------------------
     |  MONTHLY STUDENT REGISTRATIONS (Line Chart)
     --------------------------------------------- */
    $studentData = User::where('role', 'student')
        ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
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
    $serviceData = StudentService::selectRaw('MONTH(created_at) as month, COUNT(*) as total')
        ->groupBy('month')
        ->pluck('total', 'month');

    $servicesPerMonth = array_fill(1, 12, 0);
    foreach ($serviceData as $month => $count) {
        $servicesPerMonth[$month] = $count;
    }

    return view('admin.dashboard', compact(
        'totalStudents',
        'totalCommunityUsers',
        'totalServices',
        'pendingRequests',
        'pendingStudents',
        'pendingHelpers',  
        'pendingServices',   
        'studentsPerMonth',
        'servicesPerMonth',
        'studentsWithoutStatus',
    ));
}

}