<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail; 
use App\Mail\AccountBannedMail;
use App\Mail\AccountWarnedMail;

class AdminRequestController extends Controller
{
    public function index(Request $request)
{
    $query = ServiceRequest::query();

    // 1. Search Filter
    if ($request->has('search') && $request->search != '') {
        $search = $request->search;
        $query->whereHas('requester', function($q) use ($search) {
            $q->where('hu_name', 'like', "%$search%");
        })->orWhereHas('provider', function($q) use ($search) {
            $q->where('hu_name', 'like', "%$search%");
        })->orWhereHas('studentService', function($q) use ($search) {
            $q->where('hss_title', 'like', "%$search%");
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

    // Pass categories for the dropdown
    $categories = \App\Models\Category::all(); 

    return view('admin.requests.index', compact('requests', 'categories'));
}



public function resolveDispute(Request $request, $id)
{
    $serviceRequest = ServiceRequest::findOrFail($id);
    
    $action = $request->input('action_type'); 
    $targetUserId = $request->input('target_user_id');
    $note = $request->input('admin_note'); // This is the message written in the modal

    if ($action === 'dismiss') {
        $serviceRequest->update(['hsr_status' => 'cancelled']); 
        return redirect()->back()->with('success', 'Dispute dismissed.');
    }

    $user = User::findOrFail($targetUserId);

    if ($action === 'warn') {
        $user->increment('hu_warning_count');
        
        // Email
        Mail::to($user->hu_email)->send(new AccountWarnedMail($user, $note));
        
        // In-App Notification (Assuming AdminWarningNotification exists)
        $user->notify(new \App\Notifications\AdminWarningNotification($user->hu_warning_count, $note));

        // RESUME the request (instead of cancelling)
        $serviceRequest->update(['hsr_status' => 'waiting_payment']);

        $message = "User warned. Request resumed to Waiting Payment.";

    } elseif ($action === 'ban') {
       
        if ($user->hu_role === 'community') {
            $user->update(['hu_is_blacklisted' => 1, 'hu_blacklist_reason' => $note]);
            Mail::to($user->hu_email)->send(new \App\Mail\UserBlacklisted($user, $note)); // Ensure fully qualified or imported
        } else {
            $user->update(['hu_is_suspended' => 1, 'hu_blacklist_reason' => $note]);
            Mail::to($user->hu_email)->send(new AccountBannedMail($user, $note));
        }
        
        // Cancel the request if banned
        $serviceRequest->update(['hsr_status' => 'cancelled']);
        
        $message = "User has been banned. Request cancelled.";
    }

    return redirect()->route('admin.requests.index')->with('success', $message);
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