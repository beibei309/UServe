<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\StudentStatus;
use Illuminate\Http\Request;
use App\Mail\GraduationReminderMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Carbon;
use App\Notifications\StudyDurationReminder; // Make sure this import exists

class AdminStudentStatusController extends Controller
{
    // 1. DISPLAY ALL STUDENTS & HELPERS WITH STATUS
    public function index(Request $request)
    {
        $filter = $request->input('grad_filter');
        $search = $request->input('search');

        $students = User::whereIn('hu_role', ['student', 'helper'])
            ->with('studentStatus')

            // SEARCH
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                                        $q->where('hu_name', 'like', "%{$search}%")
                                            ->orWhere('hu_student_id', 'like', "%{$search}%");
                });
            })

            // GRADUATION FILTER
            ->when($filter, function ($query, $filter) {
                $query->whereHas('studentStatus', function ($q) use ($filter) {

                    $q->whereNotNull('hss_graduation_date')
                      ->where('hss_status', '!=', 'Graduated');

                    if ($filter === 'expired') {
                        $q->whereDate('hss_graduation_date', '<', today());
                    } elseif ($filter === '3_months') {
                        $q->whereBetween('hss_graduation_date', [today(), today()->addMonths(3)]);
                    } elseif ($filter === '6_months') {
                        $q->whereBetween('hss_graduation_date', [today(), today()->addMonths(6)]);
                    } elseif ($filter === '12_months') {
                        $q->whereBetween('hss_graduation_date', [today(), today()->addMonths(12)]);
                    }
                });
            })

            ->orderBy('hu_name', 'asc')
            ->paginate(10);

        $students->appends($request->all());
        $statusBadgeClasses = [
            'active' => 'bg-green-100 text-green-800',
            'probation' => 'bg-red-100 text-red-800',
            'graduated' => 'bg-blue-100 text-blue-800',
            'deferred' => 'bg-yellow-100 text-yellow-800',
            '' => 'bg-gray-100 text-gray-400',
        ];
        $today = now();
        $threeMonthsLimit = now()->addMonths(3);

        $students->getCollection()->transform(function (User $student) use ($statusBadgeClasses, $today, $threeMonthsLimit) {
            $studentStatus = $student->studentStatus;
            $statusRaw = strtolower((string) optional($studentStatus)->hss_status);
            $student->status_key = $statusRaw;
            $student->status_label = $statusRaw === '' ? 'Not Set' : ucfirst($statusRaw);
            $student->status_badge_class = $statusBadgeClasses[$statusRaw] ?? 'bg-gray-100 text-gray-800';
            $student->semester_display = $studentStatus && $studentStatus->hss_status === 'Graduated'
                ? '-'
                : ($studentStatus->hss_semester ?? '-');

            $graduationDateRaw = optional($studentStatus)->hss_graduation_date;
            $student->graduation_date_display = $graduationDateRaw
                ? Carbon::parse($graduationDateRaw)->format('d M Y')
                : '-';

            $student->show_reminder = false;
            if ($graduationDateRaw && $studentStatus->hss_status !== 'Graduated') {
                $gradDate = Carbon::parse($graduationDateRaw);
                $student->show_reminder = $gradDate->gte($today) && $gradDate->lte($threeMonthsLimit);
            }

            return $student;
        });

        return view('admin.student_status.index', compact('students'));
    }

    // 2. SHOW CREATE FORM
    public function create(Request $request)
    {
        $existingStatusIds = StudentStatus::pluck('hss_student_id')->toArray();

        $students = User::whereIn('hu_role', ['student', 'helper'])
            ->whereNotIn('hu_id', $existingStatusIds)
            ->orderBy('hu_name', 'asc')
            ->get();

        $selectedStudentId = $request->input('student_id');
        $selectedStudent = null;

        if ($selectedStudentId) {
            // Fetch the student regardless of whether they have a status (for the display box)
            $selectedStudent = User::find($selectedStudentId);
        }

        return view('admin.student_status.create', compact('students', 'selectedStudentId', 'selectedStudent'));
    }


    // 3. STORE NEW STATUS
    public function store(Request $request)
{
    $request->validate([
            'student_id' => 'required|unique:h2u_student_statuses,hss_student_id',
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
        'hss_student_id'      => $student->hu_id,
        'hss_matric_no'       => $student->hu_student_id,
        'hss_semester'        => $semester, // ✅ NEVER NULL
        'hss_status'          => $request->status,
        'hss_effective_date'  => now(),
        'hss_graduation_date' => $request->graduation_date,
    ]);

    return redirect()
        ->route('admin.student_status.index')
        ->with('success', 'Student status created successfully.');
}


    // 4. SHOW EDIT FORM
    public function edit($id)
    {
        $status = StudentStatus::findOrFail($id);

        $students = User::whereIn('hu_role', ['student', 'helper'])->get();

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

        $incomingStatus = $request->status;
        $updateData = [
            'hss_status' => $incomingStatus,
        ];

        if (in_array($incomingStatus, ['Graduated', 'Dismissed'], true)) {
            $updateData['hss_semester'] = 'Final';
        } elseif ($request->filled('semester')) {
            $updateData['hss_semester'] = trim((string) $request->input('semester'));
        }

        if ($incomingStatus === 'Dismissed') {
            $updateData['hss_graduation_date'] = null;
        } elseif ($request->filled('graduation_date')) {
            $updateData['hss_graduation_date'] = $request->graduation_date;
        }

        $statusRecord->update($updateData);

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
        
        if (!$status || !$status->hss_graduation_date) {
            return back()->with('error', 'Student has no graduation date set.');
        }

        // Send the email
        // Make sure you have created the Mailable (see step 3)
        try {
            Mail::to($user->hu_email)->send(new GraduationReminderMail($user));
            return back()->with('success', 'Reminder email sent to ' . $user->hu_name);
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

}
