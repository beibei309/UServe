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

    $students = User::whereIn('hu_role', ['student', 'helper'])
        ->with('studentStatus')

        // SEARCH
        ->when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hu_name', 'like', "%{$search}%")
                                    ->orWhere('hu_email', 'like', "%{$search}%")
                                    ->orWhere('hu_phone', 'like', "%{$search}%")
                                    ->orWhere('hu_student_id', 'like', "%{$search}%")
                                    ->orWhere('hu_skills', 'like', "%{$search}%");
            });
        })

        // STATUS FILTERS
        ->when($status === 'banned', function ($query) {
            $query->where('hu_is_suspended', 1);
        })

        ->when($status === 'student', function ($query) {
            $query->where('hu_role', 'student')
                ->where('hu_is_suspended', 0);
        })

        ->when($status === 'helper' || $status === 'helpers', function ($query) {
            $query->where('hu_role', 'helper')
                ->where('hu_is_suspended', 0);
        })

        // FACULTY FILTER
        ->when($faculty, function ($query) use ($faculty) {
            $query->where('hu_faculty', $faculty);
        })

        // DEFAULT SORT
        ->orderBy('hu_name', 'asc')
        ->paginate(10);

    // Preserve filters in pagination
    $students->appends($request->only('search', 'status', 'faculty'));

    return view('admin.students.index', compact('students', 'search', 'status', 'faculty'));
}


    // VIEW STUDENT (PROFILE PAGE)
    public function view($id)
{
    $student = User::with('studentStatus')
        ->whereIn('hu_role', ['student', 'helper'])
        ->findOrFail($id);

    return view('admin.students.view', compact('student'));
}


    // show EDIT STUDENT page
    public function edit($id)
{
        $student = User::whereIn('hu_role', ['student', 'helper'])
        ->findOrFail($id);

    return view('admin.students.edit', compact('student'));
}

    //proccess edit request
    public function update(Request $request, $id)
{
    // Only allow student OR helper
    $student = User::whereIn('hu_role', ['student', 'helper'])->findOrFail($id);

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
        'hu_name' => $request->name,
        'hu_email' => $request->email,
        'hu_phone' => $request->phone,
        'hu_student_id' => $request->student_id,
        'hu_faculty' => $request->faculty,
        'hu_course' => $request->course,
        'hu_verification_status' => $request->verification_status,

        // Helper fields
        'hu_skills' => $request->skills,
        'hu_work_experience_message' => $request->work_experience_message,
    ]);

    return redirect()
        ->route('admin.students.index')
        ->with('success', 'Student profile updated successfully.');
}

    // DELETE STUDENT
    public function destroy($id)
{
    $student = User::whereIn('hu_role', ['student', 'helper'])->findOrFail($id);

    if ($student->hu_role === 'helper' && !$student->hu_is_suspended) {
        return redirect()
            ->route('admin.students.index')
            ->with('error', 'Active helper cannot be deleted. Please ban the account first.');
    }

    if ($student->studentStatus) {
        $student->studentStatus->delete();
    }

    if ($student->hu_profile_photo_path) {
        \Illuminate\Support\Facades\Storage::delete($student->hu_profile_photo_path);
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
    $student = User::whereIn('hu_role', ['student', 'helper'])->findOrFail($id);

    // Validate reason
    $request->validate([
        'blacklist_reason' => 'required|string|max:255',
    ]);

    // Ban user
    $student->update([
        'hu_is_suspended' => 1,
        'hu_blacklist_reason' => $request->blacklist_reason,
    ]);

    Mail::to($student->hu_email)->send(new AccountBannedMail($student, $request->blacklist_reason));

    return redirect()->route('admin.students.index')
        ->with('success', 'User banned and email notification sent.');
}


    // UNBAN STUDENT
    public function unban($id)
{
    $student = User::with('studentStatus')
            ->whereIn('hu_role', ['student', 'helper'])
        ->findOrFail($id);

    // ❌ Prevent unbanning graduated users
    if (
        $student->studentStatus &&
        $student->studentStatus->hss_status === 'Graduated'
    ) {
        return redirect()
            ->route('admin.students.index')
            ->with('error', 'Cannot unban a graduated student.');
    }

    // Unban user
    $student->update([
        'hu_is_suspended' => 0,
        'hu_blacklist_reason' => null,
    ]);

    Mail::to($student->hu_email)->send(new AccountUnbannedMail($student));

    return redirect()->route('admin.students.index')
        ->with('success', 'User unbanned and email notification sent.');
}

// Show helper verification selfie
public function showSelfie($id)
{
    $student = User::findOrFail($id);
    
    if (!$student->hu_selfie_media_path || !Storage::disk('local')->exists($student->hu_selfie_media_path)) {
        abort(404, 'Selfie not found.');
    }
    
    return response()->file(Storage::disk('local')->path($student->hu_selfie_media_path));
}

// Revoke helper status
public function revokeHelper($id)
{
    $student = User::findOrFail($id);
    
    if ($student->hu_role !== 'helper') {
        return redirect()->back()->with('error', 'User is not a seller.');
    }

   
    StudentService::where('hss_user_id', $id)->delete();
    
    // 2. Revoke the role
    $student->update([
        'hu_role' => 'student',
        'hu_helper_verified_at' => null
    ]);
    
    return redirect()->back()->with('success', 'Seller status revoked and all associated services have been deleted.');
}

public function export(Request $request)
{
    $format = $request->get('format', 'csv');

    $query = User::whereIn('hu_role', ['student', 'helper']);

    // SEARCH
    if ($request->filled('search')) {
        $query->where(function($q) use ($request) {
                        $q->where('hu_name', 'like', '%'.$request->search.'%')
                            ->orWhere('hu_email', 'like', '%'.$request->search.'%')
                            ->orWhere('hu_phone', 'like', '%'.$request->search.'%')
                            ->orWhere('hu_student_id', 'like', '%'.$request->search.'%')
              ->orWhere('hu_skills', 'like', '%'.$request->search.'%');
        });
    }

    // STATUS FILTER
    if ($request->filled('status')) {
        if ($request->status == 'active') {
            $query->where('hu_is_suspended', 0);
        } elseif ($request->status == 'banned') {
            $query->where('hu_is_suspended', 1);
        } elseif ($request->status == 'student') {
            $query->where('hu_role', 'student')->where('hu_is_suspended', 0);
        } elseif ($request->status == 'helper') {
            $query->where('hu_role', 'helper')->where('hu_is_suspended', 0);
        }
    }

    $students = $query->get();

    if ($format == 'pdf') {
        $pdf = Pdf::loadView('admin.students.export_pdf', compact('students'));
        return $pdf->download('students.pdf');
    } else {
        $csvData = $students->map(function ($student) {
            return [
                'Name' => $student->hu_name,
                'Email' => $student->hu_email,
                'Phone' => $student->hu_phone,
                'Student ID' => $student->hu_student_id,
                'Status' => $student->hu_is_suspended ? 'Banned' : ($student->hu_verification_status == 'approved' ? 'Verified' : 'Not Verified'),
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