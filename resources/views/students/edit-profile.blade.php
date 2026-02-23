@extends('layouts.helper')

@section('content')
    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Edit Profile</h1>
                <p class="mt-1 text-sm text-gray-600">Update your public information and managing your account settings.</p>
            </div>

            <form method="POST" action="{{ route('students.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    {{-- Left Column: Main Inputs --}}
                    <div class="lg:col-span-2 space-y-8">

                        {{-- Basic Information --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">Basic Information</h2>

                            <div class="space-y-6">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                                    <input type="text" name="name" id="name"
                                        value="{{ old('name', $user->name) }}"
                                        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('name')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700">Email
                                        Address</label>
                                    <input type="email" value="{{ $user->email }}" disabled
                                        class="mt-1 block w-full rounded-lg border-gray-200 bg-gray-100 text-gray-500 cursor-not-allowed sm:text-sm">
                                    <p class="mt-1 text-xs text-gray-500">Email cannot be changed directly.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <label for="faculty"
                                            class="block text-sm font-medium text-gray-700">Faculty</label>
                                        <select name="faculty" id="faculty"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                            <option value="">Select Faculty</option>
                                            <option value="Fakulti Komputeran dan Meta-Teknologi"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Komputeran dan Meta-Teknologi' ? 'selected' : '' }}>
                                                Fakulti Komputeran dan Meta-Teknologi
                                            </option>
                                            <option value="Fakulti Bahasa dan Komunikasi"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Bahasa dan Komunikasi' ? 'selected' : '' }}>
                                                Fakulti Bahasa dan Komunikasi
                                            </option>
                                            <option value="Fakulti Pembangunan Manusia"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Pembangunan Manusia' ? 'selected' : '' }}>
                                                Fakulti Pembangunan Manusia
                                            </option>
                                            <option value="Fakulti Sains dan Matematik"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Sains dan Matematik' ? 'selected' : '' }}>
                                                Fakulti Sains dan Matematik
                                            </option>
                                            <option value="Fakulti Pengurusan dan Ekonomi"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Pengurusan dan Ekonomi' ? 'selected' : '' }}>
                                                Fakulti Pengurusan dan Ekonomi
                                            </option>
                                            <option value="Fakulti Sains Kemanusiaan"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Sains Kemanusiaan' ? 'selected' : '' }}>
                                                Fakulti Sains Kemanusiaan
                                            </option>
                                            <option value="Fakulti Muzik dan Seni Persembahan"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Muzik dan Seni Persembahan' ? 'selected' : '' }}>
                                                Fakulti Muzik dan Seni Persembahan
                                            </option>
                                            <option value="Fakulti Seni, Komputeran dan Industri Kreatif"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Seni, Komputeran dan Industri Kreatif' ? 'selected' : '' }}>
                                                Fakulti Seni, Komputeran dan Industri Kreatif
                                            </option>
                                            <option value="Fakulti Sains Sukan dan Kejurulatihan"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Sains Sukan dan Kejurulatihan' ? 'selected' : '' }}>
                                                Fakulti Sains Sukan dan Kejurulatihan
                                            </option>
                                            <option value="Fakulti Teknikal dan Vokasional"
                                                {{ old('faculty', $user->faculty) == 'Fakulti Teknikal dan Vokasional' ? 'selected' : '' }}>
                                                Fakulti Teknikal dan Vokasional
                                            </option>
                                        </select>
                                        @error('faculty')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="course" class="block text-sm font-medium text-gray-700">Course /
                                            Program</label>
                                        <input type="text" name="course" id="course"
                                            value="{{ old('course', $user->course) }}"
                                            placeholder="e.g. Software Engineering"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @error('course')
                                            <span class="text-red-500 text-xs">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- About You --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">About You</h2>
                            <div>
                                <label for="bio" class="block text-sm font-medium text-gray-700">Bio /
                                    Description</label>
                                <div class="mt-1">
                                    <textarea id="bio" name="bio" rows="5"
                                        class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                        placeholder="Tell us about yourself, your skills, and what services you offer...">{{ old('bio', $user->bio) }}</textarea>
                                </div>
                                <p class="mt-2 text-sm text-gray-500">Brief description for your profile. URLs are
                                    hyperlinked.</p>
                                @error('bio')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Skills --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Skills</h2>
                            <div>
                                <label for="skills" class="block text-sm font-medium text-gray-700">Skills (Comma
                                    Separated)</label>
                                <input type="text" name="skills" id="skills"
                                    value="{{ old('skills', $user->skills) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="Graphic Design, Data Entry, Photography">
                                <p class="mt-2 text-sm text-gray-500">Separate each skill with a comma.</p>
                                @error('skills')
                                    <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Work Experience --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                            <h2 class="text-xl font-semibold text-gray-900 mb-4">Work Experience</h2>

                            <div class="space-y-6">
                                {{-- Experience Message --}}
                                <div>
                                    <label for="work_experience_message"
                                        class="block text-sm font-medium text-gray-700">Experience Description</label>
                                    <div class="mt-1">
                                        <textarea id="work_experience_message" name="work_experience_message" rows="4"
                                            class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-lg"
                                            placeholder="Describe your past work experience, projects, or achievements...">{{ old('work_experience_message', $user->work_experience_message) }}</textarea>
                                    </div>
                                    <p class="mt-2 text-sm text-gray-500">Share details about your relevant work history.
                                    </p>
                                    @error('work_experience_message')
                                        <span class="text-red-500 text-xs">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Work Experience File Upload --}}
                                <div>
                                    <label for="work_experience_file"
                                        class="block text-sm font-medium text-gray-700">Supporting Document
                                        (Resume/CV/Portfolio)</label>

                                    <div
                                        class="mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:bg-gray-50 transition-colors">
                                        <div class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor"
                                                fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path
                                                    d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="work_experience_file"
                                                    class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                    <span>Upload a file</span>
                                                    <input id="work_experience_file" name="work_experience_file"
                                                        type="file" class="sr-only">
                                                </label>
                                                <p class="pl-1">or drag and drop</p>
                                            </div>
                                            <p class="text-xs text-gray-500">PDF, DOC, DOCX up to 10MB</p>
                                        </div>
                                    </div>

                                    {{-- Display existing file if present --}}
                                    @if ($user->work_experience_file)
    <div class="mt-3 flex items-center justify-between p-2 bg-indigo-50 rounded-lg border border-indigo-100">
        <div class="flex items-center truncate">
            <svg class="w-5 h-5 text-indigo-500 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                </path>
            </svg>
            <span class="text-sm text-indigo-700 truncate">
                Current: <a href="{{ asset('storage/' . $user->work_experience_file) }}" target="_blank"
                    class="underline hover:text-indigo-900 font-medium">View Document</a>
            </span>
        </div>

        {{-- Delete Button (Triggers SweetAlert) --}}
        <button type="button" onclick="confirmDeleteFile()"
            class="ml-4 flex-shrink-0 text-sm text-red-600 hover:text-red-800 font-medium hover:underline focus:outline-none">
            Remove
        </button>
    </div>
@endif

@error('work_experience_file')
    <span class="text-red-500 text-xs mt-2 block">{{ $message }}</span>
@enderror   
                            
 </div>
                            </div>
                        </div>
                    </div>

                    {{-- Right Column: Actions --}}
                    <div class="lg:col-span-1 space-y-8">

                        {{-- [REMOVED] Profile Photo Card was here --}}

                        {{-- Action Buttons --}}
                        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm sticky top-8">
                            <div class="flex flex-col gap-3">
                                <button type="submit"
                                    class="w-full flex justify-center py-2 px-4 border border-custom-teal rounded-lg shadow-sm text-sm font-medium text-white bg-custom-teal hover:bg-white hover:text-custom-teal focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-custom-teal transition-colors">
                                    Save Changes
                                </button>

                                <a href="{{ route('students.index', $user->id) }}"
                                    class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none transition-colors">
                                    Cancel
                                </a>
                            </div>
                        </div>

                    </div>

                </div>
            </form>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Hidden Form for Deleting File --}}
<form id="delete-file-form" action="{{ route('students.delete-file') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDeleteFile() {
        Swal.fire({
            title: 'Delete File?',
            text: "Are you sure you want to remove your document? This cannot be undone.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-file-form').submit();
            }
        })
    }
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // Jika ada session success dari controller ? Tunjuk SweetAlert
            @if (session('success'))
                Swal.fire({
                    title: "Successfull!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                });
            @endif

            // Confirmation sebelum submit form
            const form = document.querySelector('form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Save Changes?',
                        text: "Are you sure you want to update your profile?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#4F46E5',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, Save Changes',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit(); // <-- Normal submit, bukan AJAX
                        }
                    });
                });
            }
        });
    </script>
@endsection