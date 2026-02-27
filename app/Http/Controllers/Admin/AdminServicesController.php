<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentService;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\WarningMail;
use App\Mail\ServiceSuspendedMail;
use App\Mail\ServiceApprovedMail; 
use App\Mail\ServiceRejectedMail; 

// Import the Notification
use App\Notifications\ServiceStatusNotification;


class AdminServicesController extends Controller
{
 public function index(Request $request)
{
    $search     = $request->query('search');
    $categoryId = $request->query('category');
    $studentId  = $request->query('student');
    $status     = $request->query('status');
    $rating = $request->query('rating');


    $services = StudentService::with(['user', 'category'])
        ->withAvg('reviews as reviews_avg_rating', 'hr_rating')
        ->withCount('reviews')
        ->when($status, fn($q) => $q->where('hss_approval_status', $status))
        ->when($rating, function ($q, $rating) {
        [$min, $max] = explode('-', $rating);
        $q->havingBetween('reviews_avg_rating', [(float) $min, (float) $max]);
    })
        ->when($search, function ($query, $search) {
            $query->where(function ($q) use ($search) {
                $q->where('hss_title', 'like', "%{$search}%")
                    ->orWhere('hss_description', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('hu_name', 'like', "%{$search}%");
                    });
            });
        })
        ->when($categoryId, fn($q) => $q->where('hss_category_id', $categoryId))
        ->when($studentId, fn($q) => $q->where('hss_user_id', $studentId))
        ->latest()
        ->paginate(10)
        ->withQueryString();

    $categories = Category::orderBy('hc_name')->get();
    $students   = User::where('hu_role', 'helper')->orderBy('hu_name')->get();

    return view('admin.services.index', compact(
        'services',
        'categories',
        'students'
    ));
}


    // Approve a service
    public function approve(StudentService $service)
    {
        $service->hss_approval_status = 'approved';
        $service->save();

        if ($service->user && $service->user->hu_email) {
            Mail::to($service->user->hu_email)->send(new ServiceApprovedMail($service));
        }

        // 2. Send Database Notification
        if ($service->user) {
            $service->user->notify(new ServiceStatusNotification('approved', $service));
        }

        return redirect()->route('admin.services.index')->with('success', 'Service approved.');
    }

    // Reject a service
    public function reject(Request $request, StudentService $service)
{
    // 1. Validate the incoming reason from the modal
    $request->validate([
        'reject_reason' => 'required|string|max:1000',
    ]);

    // 2. Update status AND reason
    $service->hss_approval_status = 'rejected';
    $service->hss_warning_reason = $request->input('reject_reason');
    $service->save();

    // 3. Send Email (Now $service contains the reject_reason)
    if ($service->user && $service->user->hu_email) {
        Mail::to($service->user->hu_email)->send(new ServiceRejectedMail($service));
    }

    // 4. Send Database Notification
    if ($service->user) {
        $service->user->notify(new ServiceStatusNotification('rejected', $service));
    }

    return redirect()->route('admin.services.index')->with('success', 'Service rejected successfully.');
}

    // Delete/Destroy service record
    public function destroy(StudentService $service)
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Service has been permanently deleted.');
    }

    // 👇 INI FUNCTION BARU UNTUK WARNING (Copy bahagian ini)
    public function storeWarning(Request $request, $id)
{
    // 1. Validasi Input
    $request->validate([
        'reason' => 'required|string|max:255',
    ]);

    // 2. Cari Servis
    $service = StudentService::findOrFail($id);
    $student = $service->user;

    // 3. Update Warning
    $service->hss_warning_count = $service->hss_warning_count + 1;
    $service->hss_warning_reason = $request->reason;

    // ❌ REMOVE AUTO SUSPEND
    // if ($service->warning_count >= 3) {
    //     $service->approval_status = 'suspended';
    // }

    $service->save();

    // 4. Hantar Email
    try {
        $emailData = [
            'student_name' => $student->hu_name,
            'service_name' => $service->hss_title,
            'reason'       => $request->reason,
            'count'        => $service->hss_warning_count
        ];

        Mail::to($student->hu_email)->send(new WarningMail($emailData));

    } catch (\Exception $e) {
        Log::error('Email warning gagal dihantar: ' . $e->getMessage());
    }

    // 5. Response UI
    if ($service->hss_warning_count >= 3) {
        return back()->with('warning', 'Student telah mencapai 3/3 warning. Sila suspend jika perlu.');
    }

    return back()->with('success', 'Warning berjaya dihantar. Jumlah warning: ' . $service->hss_warning_count);
}

public function suspend(StudentService $service)
{
    $service->hss_approval_status = 'suspended';
    $service->save();

    // Hantar Email
    if ($service->user && $service->user->hu_email) {
        Mail::to($service->user->hu_email)->send(new ServiceSuspendedMail($service));
    }

    return back()->with('error', 'Service has been suspended and email notification sent.');
}


    public function reviews($id)
    {
        $service = StudentService::with('user')->findOrFail($id);

        $reviews = Review::where('hr_student_service_id', $id)
            ->with('reviewer')
            ->latest()
            ->paginate(10);

        return view('admin.services.reviews', compact('service', 'reviews'));
    }

    public function show($id)
    {
        $service = StudentService::with(['user', 'category'])
            ->withAvg('reviews as reviews_avg_rating', 'hr_rating')
            ->withCount('reviews')
            ->findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    // UNBLOCK Service
    public function unblock(StudentService $service)
    {
        $service->hss_approval_status = 'approved';
        $service->hss_warning_count = 0; // optional kalau nak reset warning
        $service->save();

        // Notify user (optional kalau nak)
        if ($service->user) {
            $service->user->notify(new ServiceStatusNotification('unblocked', $service));
        }

        return back()->with('success', 'Service has been unblocked and approved again.');
    }


}
