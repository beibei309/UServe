<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentStatus;
use Illuminate\Http\Request;
use App\Mail\GraduationReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Notifications\StudyDurationReminder; // Make sure this import exists

class AdminStudentStatusController extends Controller
{
    // 1. DISPLAY ALL STUDENTS & HELPERS WITH STATUS
    public function index(Request $request)
    {
        $filter = $request->input('grad_filter');
        $search = $request->input('search');

        $students = User::whereIn('role', ['student', 'helper'])
            ->with('studentStatus')

            // SEARCH
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('student_id', 'like', "%{$search}%");
                });
            })

            // GRADUATION FILTER
            ->when($filter, function ($query, $filter) {
                $query->whereHas('studentStatus', function ($q) use ($filter) {

                    $q->whereNotNull('graduation_date')
                      ->where('status', '!=', 'Graduated');

                    if ($filter === 'expired') {
                        $q->whereDate('graduation_date', '<', today());
                    } elseif ($filter === '3_months') {
                        $q->whereBetween('graduation_date', [today(), today()->addMonths(3)]);
                    } elseif ($filter === '6_months') {
                        $q->whereBetween('graduation_date', [today(), today()->addMonths(6)]);
                    } elseif ($filter === '12_months') {
                        $q->whereBetween('graduation_date', [today(), today()->addMonths(12)]);
                    }
                });
            })

            ->orderBy('name', 'asc')
            ->paginate(10);

        $students->appends($request->all());

        return view('admin.student_status.index', compact('students'));
    }

    // 2. SHOW CREATE FORM
    public function create(Request $request)
{
    $existingStatusIds = StudentStatus::pluck('student_id')->toArray();

    $students = User::whereIn('role', ['student', 'helper'])
        ->whereNotIn('id', $existingStatusIds)
        ->orderBy('name', 'asc')
        ->get();

    $selectedStudentId = $request->input('student_id');

    return view('admin.student_status.create', compact('students', 'selectedStudentId'));
}


    // 3. STORE NEW STATUS
    public function store(Request $request)
{
    $request->validate([
        'student_id' => 'required|unique:student_statuses,student_id',
        'status' => 'required|in:Active,Probation,Deferred,Graduated,Dismissed',
        'semester' => 'nullable|string',
        'graduation_date' => 'nullable|date',
    ]);

    $student = User::findOrFail($request->student_id);

    // Graduation requires date
    if ($request->status === 'Graduated' && !$request->graduation_date) {
        return back()
            ->withInput()
            ->withErrors([
                'graduation_date' => 'Graduation date is required for graduated students.'
            ]);
    }

    // ✅ FORCE semester value (NO NULL)
    if (in_array($request->status, ['Graduated', 'Dismissed'])) {
        $semester = 'Final';
    } else {
        $semester = $request->semester;
    }

    if (!$semester) {
        return back()
            ->withInput()
            ->withErrors([
                'semester' => 'Semester is required.'
            ]);
    }

    StudentStatus::create([
        'student_id'      => $student->id,
        'matric_no'       => $student->student_id,
        'semester'        => $semester, // ✅ NEVER NULL
        'status'          => $request->status,
        'effective_date'  => now(),
        'graduation_date' => $request->graduation_date,
    ]);

    return redirect()
        ->route('admin.student_status.index')
        ->with('success', 'Student status created successfully.');
}


    // 4. SHOW EDIT FORM
    public function edit($id)
    {
        $status = StudentStatus::findOrFail($id);

        $students = User::whereIn('role', ['student', 'helper'])->get();

        return view('admin.student_status.edit', compact('status', 'students'));
    }

    // 5. UPDATE STATUS
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required',
            'semester' => 'nullable|string',
            'graduation_date' => 'nullable|date',
        ]);

        $statusRecord = StudentStatus::findOrFail($id);

        $statusRecord->update([
            'semester' => $request->semester,
            'status' => $request->status,
            'graduation_date' => $request->graduation_date,
        ]);

        return redirect()->route('admin.student_status.index')
            ->with('success', 'Student status updated.');
    }

    // 6. DELETE STATUS
    public function destroy($id)
    {
        StudentStatus::findOrFail($id)->delete();

        return redirect()->route('admin.student_status.index')
            ->with('success', 'Status deleted.');
    }

    public function sendReminder($userId)
    {
        $user = User::findOrFail($userId);
        
        // Safety check: Ensure they are actually graduating soon
        $status = $user->studentStatus;
        
        if (!$status || !$status->graduation_date) {
            return back()->with('error', 'Student has no graduation date set.');
        }

        // Send the email
        // Make sure you have created the Mailable (see step 3)
        try {
            Mail::to($user->email)->send(new GraduationReminderMail($user));
            return back()->with('success', 'Reminder email sent to ' . $user->name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

}