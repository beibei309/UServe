<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\AccountBannedMail;
use App\Mail\AccountUnbannedMail;



class AdminStudentController extends Controller
{
    public function index(Request $request)
{
    $search  = $request->input('search');
    $status  = $request->input('status'); // student | helper | banned | null
    $faculty = $request->input('faculty');

    $students = User::whereIn('role', ['student', 'helper'])
        ->with('studentStatus')

        // SEARCH
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('student_id', 'like', "%{$search}%")
                  ->orWhere('skills', 'like', "%{$search}%");
            });
        })

        // STATUS FILTERS
        ->when($status === 'banned', function ($query) {
            $query->where('is_suspended', 1);
        })

        ->when($status === 'student', function ($query) {
            $query->where('role', 'student')
                  ->where('is_suspended', 0);
        })

        ->when($status === 'helper' || $status === 'helpers', function ($query) {
            $query->where('role', 'helper')
                  ->where('is_suspended', 0);
        })

        // FACULTY FILTER
        ->when($faculty, function ($query) use ($faculty) {
            $query->where('faculty', $faculty);
        })

        // DEFAULT SORT
        ->orderBy('name', 'asc')
        ->paginate(10);

    // Preserve filters in pagination
    $students->appends($request->only('search', 'status', 'faculty'));

    return view('admin.students.index', compact('students', 'search', 'status', 'faculty'));
}


    // VIEW STUDENT (PROFILE PAGE)
    public function view($id)
{
    $student = User::with('studentStatus')
        ->whereIn('role', ['student', 'helper'])
        ->findOrFail($id);

    return view('admin.students.view', compact('student'));
}


    // show EDIT STUDENT page
    public function edit($id)
{
    $student = User::whereIn('role', ['student', 'helper'])
        ->findOrFail($id);

    return view('admin.students.edit', compact('student'));
}

    //proccess edit request
    public function update(Request $request, $id)
{
    // Only allow student OR helper
    $student = User::whereIn('role', ['student', 'helper'])->findOrFail($id);

    // VALIDATION
    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'student_id' => 'nullable|string|max:20',
        'faculty' => 'nullable|string|max:255',
        'course' => 'nullable|string|max:255',
        'verification_status' => 'required|in:pending,approved,rejected',

        // Helper profile
        'skills' => 'nullable|string',
        'work_experience_message' => 'nullable|string',
    ]);

    // UPDATE DATA
    $student->update([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'student_id' => $request->student_id,
        'faculty' => $request->faculty,
        'course' => $request->course,
        'verification_status' => $request->verification_status,

        // Helper fields
        'skills' => $request->skills,
        'work_experience_message' => $request->work_experience_message,
    ]);

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'Student profile updated successfully.');
}

    // DELETE STUDENT
    public function destroy($id)
{
    $student = User::whereIn('role', ['student', 'helper'])->findOrFail($id);

    if ($student->role === 'helper' && !$student->is_suspended) {
        return redirect()
            ->route('admin.students.index')
            ->with('error', 'Active helper cannot be deleted. Please ban the account first.');
    }

    if ($student->studentStatus) {
        $student->studentStatus->delete();
    }

    if ($student->profile_photo_path) {
        \Illuminate\Support\Facades\Storage::delete($student->profile_photo_path);
    }

    $student->delete();

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'Student account deleted successfully.');
        
}


    // BAN STUDENT WITH REASON
    public function ban(Request $request, $id)
{
    // Only student or helper can be banned
    $student = User::whereIn('role', ['student', 'helper'])->findOrFail($id);

    // Validate reason
    $request->validate([
        'blacklist_reason' => 'required|string|max:255',
    ]);

    // Ban user
    $student->update([
        'is_suspended' => 1,
        'blacklist_reason' => $request->blacklist_reason,
    ]);

    Mail::to($student->email)->send(new AccountBannedMail($student, $request->blacklist_reason));

    return redirect()->route('admin.students.index')
        ->with('success', 'User banned and email notification sent.');
}


    // UNBAN STUDENT
    public function unban($id)
{
    $student = User::with('studentStatus')
        ->whereIn('role', ['student', 'helper'])
        ->findOrFail($id);

    // ❌ Prevent unbanning graduated users
    if (
        $student->studentStatus &&
        $student->studentStatus->status === 'Graduated'
    ) {
        return redirect()
            ->route('admin.students.index')
            ->with('error', 'Cannot unban a graduated student.');
    }

    // Unban user
    $student->update([
        'is_suspended' => 0,
        'blacklist_reason' => null,
    ]);

    Mail::to($student->email)->send(new AccountUnbannedMail($student));

    return redirect()->route('admin.students.index')
        ->with('success', 'User unbanned and email notification sent.');
}

// Show helper verification selfie
public function showSelfie($id)
{
    $student = User::findOrFail($id);
    
    if (!$student->selfie_media_path || !Storage::disk('local')->exists($student->selfie_media_path)) {
        abort(404, 'Selfie not found.');
    }
    
    return Storage::disk('local')->response($student->selfie_media_path);
}

// Revoke helper status
public function revokeHelper($id)
{
    $student = User::findOrFail($id);
    
    if ($student->role !== 'helper') {
        return redirect()->back()->with('error', 'User is not a seller.');
    }

   
    StudentService::where('user_id', $id)->delete();
    
    // 2. Revoke the role
    $student->update([
        'role' => 'student',
        'helper_verified_at' => null
    ]);
    
    return redirect()->back()->with('success', 'Seller status revoked and all associated services have been deleted.');
}

public function export(Request $request)
{
    $format = $request->get('format', 'csv');

    $query = User::whereIn('role', ['student', 'helper']);

    // SEARCH
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
            $q->where('name', 'like', '%'.$request->search.'%')
              ->orWhere('email', 'like', '%'.$request->search.'%')
              ->orWhere('phone', 'like', '%'.$request->search.'%')
              ->orWhere('student_id', 'like', '%'.$request->search.'%')
              ->orWhere('skills', 'like', '%'.$request->search.'%');
        });
    }

    // STATUS FILTER
    if ($request->filled('status')) {
        if ($request->status == 'active') {
            $query->where('is_suspended', 0);
        } elseif ($request->status == 'banned') {
            $query->where('is_suspended', 1);
        } elseif ($request->status == 'student') {
            $query->where('role', 'student')->where('is_suspended', 0);
        } elseif ($request->status == 'helper') {
            $query->where('role', 'helper')->where('is_suspended', 0);
        }
    }

    $students = $query->get();

    if ($format == 'pdf') {
        $pdf = Pdf::loadView('admin.students.export_pdf', compact('students'));
        return $pdf->download('students.pdf');
    } else {
        $csvData = $students->map(function ($student) {
            return [
                'Name' => $student->name,
                'Email' => $student->email,
                'Phone' => $student->phone,
                'Student ID' => $student->student_id,
                'Status' => $student->is_suspended ? 'Banned' : ($student->verification_status == 'approved' ? 'Verified' : 'Not Verified'),
            ];
        });

        return response()->streamDownload(function() use ($csvData) {
            $output = fopen('php://output', 'w');
            if ($csvData->isNotEmpty()) {
                fputcsv($output, array_keys($csvData->first()));
                foreach ($csvData as $row) {
                    fputcsv($output, $row);
                }
            }
            fclose($output);
        }, 'students.csv');
    }
}


}