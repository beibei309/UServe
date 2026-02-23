@extends('admin.layout')

@section('content')

<div class="max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Edit Student Profile</h1>
        <a href="{{ route('admin.students.index') }}"
           class="text-gray-600 hover:text-gray-900 text-sm font-medium">
            &larr; Back to List
        </a>
    </div>

    <form action="{{ route('admin.students.update', $student->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

            {{-- LEFT COLUMN --}}
            <div class="md:col-span-2 space-y-6">

                {{-- PERSONAL INFO --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">
                        Personal Information
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Full Name
                            </label>
                            <input type="text" name="name"
                                   value="{{ old('name', $student->name) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Email Address
                            </label>
                            <input type="email" name="email"
                                   value="{{ old('email', $student->email) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            @error('email')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Phone Number
                            </label>
                            <input type="text" name="phone"
                                   value="{{ old('phone', $student->phone) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Matric / Student ID
                            </label>
                            <input type="text" name="student_id"
                                   value="{{ old('student_id', $student->student_id) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
    <label class="block text-sm font-medium text-gray-700 mb-2">
        Faculty
    </label>

    <select name="faculty" class="w-full rounded-lg border-gray-300">
    <option value="">Select Faculty</option>

    <option value="FKMT" {{ old('faculty', $student->faculty)=='FKMT' ? 'selected' : '' }}>FKMT</option>
    <option value="FPE" {{ old('faculty', $student->faculty)=='FPE' ? 'selected' : '' }}>FPE</option>
    <option value="FSKIK" {{ old('faculty', $student->faculty)=='FSKIK' ? 'selected' : '' }}>FSKIK</option>
    <option value="FSK" {{ old('faculty', $student->faculty)=='FSK' ? 'selected' : '' }}>FSK</option>
    <option value="FSMT" {{ old('faculty', $student->faculty)=='FSMT' ? 'selected' : '' }}>FSMT</option>
    <option value="FBK" {{ old('faculty', $student->faculty)=='FBK' ? 'selected' : '' }}>FBK</option>
    <option value="FPM" {{ old('faculty', $student->faculty)=='FPM' ? 'selected' : '' }}>FPM</option>
    <option value="FMUP" {{ old('faculty', $student->faculty)=='FMUP' ? 'selected' : '' }}>FMUP</option>
    <option value="FSSKJ" {{ old('faculty', $student->faculty)=='FSSKJ' ? 'selected' : '' }}>FSSKJ</option>
    <option value="FTV" {{ old('faculty', $student->faculty)=='FTV' ? 'selected' : '' }}>FTV</option>
</select>

    @error('faculty')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Course
                            </label>
                            <input type="text" name="course"
                                   value="{{ old('course', $student->course) }}"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </div>

                    </div>
                </div>

                {{-- HELPER PROFILE --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <div class="flex justify-between items-center mb-4 border-b pb-2">
                        <h2 class="text-lg font-semibold text-gray-800">
                            Helper Profile
                        </h2>
                        <span class="text-xs text-gray-500 bg-gray-100 px-2 py-1 rounded">
                            Visible to public
                        </span>
                    </div>

                    <div class="space-y-4">

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Skills (comma separated)
                            </label>
                            <input type="text" name="skills"
                                   value="{{ old('skills', $student->skills) }}"
                                   placeholder="e.g. Tutoring, Graphic Design, Python"
                                   class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500">
                            <p class="text-xs text-gray-500 mt-1">
                                Skills help identify helpers in the system.
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Experience / Description
                            </label>
                            <textarea name="work_experience_message" rows="4"
                                      class="w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                                      placeholder="Describe experience or services offered...">{{ old('work_experience_message', $student->work_experience_message) }}</textarea>
                        </div>

                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN --}}
            <div class="md:col-span-1 space-y-6">

                {{-- ACCOUNT STATUS --}}
                <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
                    <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4">
                        Account Status
                    </h2>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Verification Status
                        </label>
                        <select name="verification_status"
        class="w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">

    {{-- Pending --}}
    <option value="pending"
        @if($student->verification_status === 'approved') disabled @endif
        {{ $student->verification_status === 'pending' ? 'selected' : '' }}>
        Pending
    </option>

    {{-- Approved --}}
    <option value="approved"
        {{ $student->verification_status === 'approved' ? 'selected' : '' }}>
        Approved (Verified)
    </option>

    {{-- Rejected --}}
    <option value="rejected"
        {{ $student->verification_status === 'rejected' ? 'selected' : '' }}>
        Rejected
    </option>

</select>

@if($student->verification_status === 'approved')
    <p class="text-xs text-gray-500 mt-1">
        Once approved, the status cannot be reverted to pending.
    </p>
@endif

                    </div>

                    <hr class="my-4 border-gray-100">

                    <div>
                        <span class="block text-sm font-medium text-gray-700 mb-1">
                            Current Standing
                        </span>

                        @if($student->is_suspended)
                            <div class="p-2 bg-red-50 border border-red-200 rounded text-red-700 text-sm">
                                <strong>Banned</strong><br>
                                <span class="text-xs">
                                    Reason: {{ $student->blacklist_reason }}
                                </span>
                            </div>
                        @else
                            <div class="p-2 bg-green-50 border border-green-200 rounded text-green-700 text-sm">
                                Active
                            </div>
                        @endif
                    </div>
                </div>

                {{-- ACTION BUTTONS --}}
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 flex flex-col gap-3">
                    <button type="submit"
                            class="w-full py-2 px-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded shadow transition">
                        Save Changes
                    </button>

                    <a href="{{ route('admin.students.index') }}"
                       class="w-full py-2 px-4 bg-white border border-gray-300 text-gray-700 font-medium rounded hover:bg-gray-50 text-center transition">
                        Cancel
                    </a>
                </div>

            </div>

        </div>
    </form>

</div>

@endsection