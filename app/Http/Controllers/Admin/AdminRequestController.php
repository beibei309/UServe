<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; 
use App\Mail\AccountBannedMail;
use App\Mail\AccountWarnedMail;
use Illuminate\Support\Carbon;

class AdminRequestController extends Controller
{
    private function userWarningLimit(): int
    {
        return (int) config('moderation.user_warning_limit', 3);
    }

    public function index(Request $request)
{
    $query = ServiceRequest::query()->with(['requester', 'provider', 'studentService']);

    // 1. Search Filter
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->where(function ($outer) use ($search) {
            $outer->whereHas('requester', function($q) use ($search) {
                $q->where('hu_name', 'like', "%$search%");
            })->orWhereHas('provider', function($q) use ($search) {
                $q->where('hu_name', 'like', "%$search%");
            })->orWhereHas('studentService', function($q) use ($search) {
                $q->where('hss_title', 'like', "%$search%");
            });
        });
    }

    // 2. Status Filter
    if ($request->has('status') && $request->status != '') {
        $query->where('hsr_status', $request->status);
    }

    // 3. Category Filter
    if ($request->has('category') && $request->category != '') {
        $query->whereHas('studentService', function($q) use ($request) {
            $q->where('hss_category_id', $request->category);
        });
    }

    $requests = $query->latest()->paginate(10);
    $reportedByIds = $requests->getCollection()
        ->pluck('hsr_reported_by')
        ->filter()
        ->unique()
        ->values();
    $reportedByUsers = User::whereIn('hu_id', $reportedByIds)->get()->keyBy('hu_id');
    $warningLimit = $this->userWarningLimit();

    $statusStyles = [
        'pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'in_progress' => 'bg-blue-100 text-blue-800 border-blue-200',
        'completed' => 'bg-green-100 text-green-800 border-green-200',
        'disputed' => 'bg-red-100 text-red-800 border-red-200 animate-pulse',
        'cancelled' => 'bg-gray-100 text-gray-600 border-gray-200',
        'rejected' => 'bg-gray-100 text-gray-600 border-gray-200',
    ];

    $requests->getCollection()->transform(function (ServiceRequest $serviceRequest) use ($reportedByUsers, $statusStyles) {
        $selectedPackage = $serviceRequest->hsr_selected_package;
        $serviceRequest->selected_package_label = is_array($selectedPackage)
            ? implode(', ', array_filter($selectedPackage))
            : ($selectedPackage ?? '');

        $selectedDates = $serviceRequest->hsr_selected_dates;
        $selectedDateValues = is_array($selectedDates)
            ? array_values(array_filter($selectedDates))
            : (filled($selectedDates) ? [$selectedDates] : []);
        $serviceRequest->selected_date_values = $selectedDateValues;
        $serviceRequest->first_selected_date = $selectedDateValues[0] ?? null;
        $serviceRequest->first_selected_date_display = $serviceRequest->first_selected_date
            ? Carbon::parse($serviceRequest->first_selected_date)->format('d M Y')
            : 'Not set';
        $serviceRequest->created_at_human = $serviceRequest->created_at
            ? $serviceRequest->created_at->diffForHumans()
            : '-';

        $serviceRequest->status_style = $statusStyles[$serviceRequest->hsr_status] ?? 'bg-gray-100 text-gray-600 border-gray-200';
        $serviceRequest->status_label = str_replace('_', ' ', $serviceRequest->hsr_status);
        $serviceRequest->service_initial = substr((string) optional($serviceRequest->studentService)->hss_title, 0, 1);

        $reporterName = 'Unknown';
        $reporterRole = 'System';
        $reporter = $reportedByUsers->get($serviceRequest->hsr_reported_by);
        if ($reporter) {
            $reporterName = $reporter->hu_name;
            if ((int) $serviceRequest->hsr_reported_by === (int) $serviceRequest->hsr_requester_id) {
                $reporterRole = 'Buyer';
            } elseif ((int) $serviceRequest->hsr_reported_by === (int) $serviceRequest->hsr_provider_id) {
                $reporterRole = 'Seller';
            } else {
                $reporterRole = 'Admin';
            }
        }

        $serviceRequest->reporter_payload = ['name' => $reporterName, 'role' => $reporterRole];
        $serviceRequest->requester_payload = [
            'id' => $serviceRequest->requester->hu_id,
            'name' => $serviceRequest->requester->hu_name,
            'warnings' => $serviceRequest->requester->hu_warning_count,
            'role' => $serviceRequest->requester->hu_role,
        ];
        $serviceRequest->provider_payload = [
            'id' => $serviceRequest->provider->hu_id,
            'name' => $serviceRequest->provider->hu_name,
            'warnings' => $serviceRequest->provider->hu_warning_count,
            'role' => $serviceRequest->provider->hu_role,
        ];

        return $serviceRequest;
    });

    // Pass categories for the dropdown
    $categories = Category::all(); 

    return view('admin.requests.index', compact('requests', 'categories', 'warningLimit'));
}



public function resolveDispute(Request $request, $id)
{
    $serviceRequest = ServiceRequest::findOrFail($id);
    
    $action = $request->input('action_type'); 
    $targetUserId = $request->input('target_user_id');
    $note = $request->input('admin_note'); // This is the message written in the modal
    $message = 'Action completed.';

    if ($action === 'dismiss') {
        $serviceRequest->update(['hsr_status' => 'cancelled']); 
        return redirect()->back()->with('success', 'Dispute dismissed without penalty. Request marked as cancelled.');
    }

    if ($action === 'resume') {
        $serviceRequest->update(['hsr_status' => 'waiting_payment']);
        return redirect()->back()->with('success', 'Dispute closed without penalty. Request resumed to Waiting Payment.');
    }

    if ($action === 'complete_paid') {
        $serviceRequest->update([
            'hsr_status' => 'completed',
            'hsr_payment_status' => 'paid',
        ]);
        return redirect()->back()->with('success', 'Dispute closed without penalty. Request marked as Completed (Paid).');
    }

    $user = User::findOrFail($targetUserId);

    if ($action === 'warn') {
        $limit = $this->userWarningLimit();
        if ((int) $user->hu_warning_count >= $limit) {
            return redirect()->route('admin.requests.index')->with('warning', "User already reached {$limit} warnings. Use Suspend/Blacklist instead.");
        }

        $user->increment('hu_warning_count');
        
        // Email
        Mail::to($user->hu_email)->send(new AccountWarnedMail($user, $note));
        
        // In-App Notification (Assuming AdminWarningNotification exists)
        $user->notify(new \App\Notifications\AdminWarningNotification($user->hu_warning_count, $note));

        // RESUME the request (instead of cancelling)
        $serviceRequest->update(['hsr_status' => 'waiting_payment']);

        $limit = $this->userWarningLimit();
        $remaining = max(0, $limit - (int) $user->hu_warning_count);
        $message = $remaining > 0
            ? "User warned. {$remaining} warning(s) left before restriction. Request resumed to Waiting Payment."
            : "User warned and reached warning limit. Request resumed to Waiting Payment.";

    } elseif ($action === 'suspend_or_blacklist' || $action === 'restrict' || $action === 'ban') {
       
        if ($user->hu_role === 'community') {
            $user->update(['hu_is_blacklisted' => 1, 'hu_is_suspended' => 0, 'hu_blacklist_reason' => $note]);
            Mail::to($user->hu_email)->send(new \App\Mail\UserBlacklisted($user, $note)); // Ensure fully qualified or imported
            $message = "User blacklisted. Request cancelled.";
        } else {
            $user->update(['hu_is_suspended' => 1, 'hu_is_blacklisted' => 0, 'hu_blacklist_reason' => $note]);
            Mail::to($user->hu_email)->send(new AccountBannedMail($user, $note));
            $message = "User suspended. Request cancelled.";
        }
        
        // Cancel the request if banned
        $serviceRequest->update(['hsr_status' => 'cancelled']);
    }

    return redirect()->route('admin.requests.index')->with('success', $message);
}
    
    public function destroy($id)
    {
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->delete();

        return redirect()->route('admin.requests.index')->with('success', 'Service request deleted successfully.');
    }

    public function export(Request $request)
{
    $query = \App\Models\ServiceRequest::with(['requester', 'provider', 'studentService']);

    // Apply search filter
    if ($request->filled('search')) {
        $search = $request->search;
          $query->whereHas('requester', fn($q) => $q->where('hu_name', 'like', "%{$search}%"))
              ->orWhereHas('studentService', fn($q) => $q->where('hss_title', 'like', "%{$search}%"));
    }

    // Apply status filter
    if ($request->filled('status')) {
        $status = $request->status;
        $query->where('hsr_status', $status);
    }

    $requests = $query->get();

    $csvData = $requests->map(function ($r) {
        return [
            'Requester' => $r->requester->hu_name,
            'Service' => $r->studentService->hss_title,
            'Provider' => $r->provider->hu_name,
            'Request Date' => $r->created_at->format('d/m/Y'),
            'Price' => number_format((float) $r->studentService->hss_suggested_price, 2),
            'Status' => $r->hsr_status,
        ];
    });

    return response()->streamDownload(function () use ($csvData) {
        $handle = fopen('php://output', 'w');
        fputcsv($handle, array_keys($csvData->first()));
        foreach ($csvData as $row) {
            fputcsv($handle, $row);
        }
        fclose($handle);
    }, 'service_requests.csv');
}

}
