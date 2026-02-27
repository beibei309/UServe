<x-app-layout>
    <div class="bg-gray-50 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
        
        <div class="max-w-3xl mx-auto text-center mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Become a Student Seller</h1>
            <p class="mt-2 text-gray-600">Share your skills and earn extra income by helping others in the UPSI community.</p>
        </div>

        <div class="max-w-3xl mx-auto bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
            
            <div class="h-2 bg-indigo-100 w-full">
                <div class="h-2 bg-indigo-600 w-2/3 rounded-r-full"></div> </div>

            <div class="p-8 sm:p-12">
                @if (session('status'))
                    <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('students.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <div class="flex flex-col items-center sm:flex-row sm:items-start gap-6 pb-8 border-b border-gray-100">
                        <div class="relative group">
                            <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100">
                                <img id="profile-photo-preview" 
                                      src="{{ auth()->user()->hu_profile_photo_path ? asset(auth()->user()->hu_profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->hu_name) }}" 
                                     alt="Profile Photo" 
                                     class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
                            </div>
                            <label for="profile_photo_input" class="absolute bottom-0 right-0 bg-indigo-600 text-white p-2 rounded-full cursor-pointer shadow-md hover:bg-indigo-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </label>
                            <input type="file" id="profile_photo_input" name="profile_photo" accept="image/*" class="hidden" />
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-lg font-semibold text-gray-900">Profile Picture</h3>
                            <p class="text-sm text-gray-500 mt-1">Upload a professional photo to build trust. JPG or PNG, max 4MB.</p>
                            @error('profile_photo')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2">About Yourself</label>
                        <div class="relative">
                            <textarea id="bio" name="bio" rows="4" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors py-3 px-4 text-gray-800 placeholder-gray-400"
                                placeholder="Hi, I'm a computer science student passionate about web design...">{{ old('bio', auth()->user()->hu_bio) }}</textarea>
                            <div class="absolute bottom-3 right-3 text-xs text-gray-400 pointer-events-none">Tell us your story</div>
                        </div>
                        @error('bio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                            <h3 class="font-semibold text-gray-900">Education Details</h3>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Faculty</label>
                                <select name="faculty" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm bg-white">
                                    <option value="">Select Faculty</option>
                                    <option value="Fakulti Komputeran & Meta-Teknologi" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Komputeran & Meta-Teknologi' ? 'selected' : '' }}>Fakulti Komputeran & Meta-Teknologi</option>
                                    <option value="Fakulti Pengurusan & Ekonomi" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Pengurusan & Ekonomi' ? 'selected' : '' }}>Fakulti Pengurusan & Ekonomi</option>
                                    <option value="Fakulti Seni, Kelestarian & Industri Kreatif" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Seni, Kelestarian & Industri Kreatif' ? 'selected' : '' }}>Fakulti Seni, Kelestarian & Industri Kreatif</option>
                                    <option value="Fakulti Sains Kemanusiaan" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Sains Kemanusiaan' ? 'selected' : '' }}>Fakulti Sains Kemanusiaan</option>
                                    <option value="Fakulti Sains & Matematik" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Sains & Matematik' ? 'selected' : '' }}>Fakulti Sains & Matematik</option>
                                    <option value="Fakulti Bahasa & Komunikasi" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Bahasa & Komunikasi' ? 'selected' : '' }}>Fakulti Bahasa & Komunikasi</option>
                                    <option value="Fakulti Pembangunan Manusia" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Pembangunan Manusia' ? 'selected' : '' }}>Fakulti Pembangunan Manusia</option>
                                    <option value="Fakulti Muzik & Seni Persembahan" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Muzik & Seni Persembahan' ? 'selected' : '' }}>Fakulti Muzik & Seni Persembahan</option>
                                    <option value="Fakulti Sains Sukan & Kejurulatihan" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Sains Sukan & Kejurulatihan' ? 'selected' : '' }}>Fakulti Sains Sukan & Kejurulatihan</option>
                                    <option value="Fakulti Teknikal & Vokasional" {{ old('faculty', auth()->user()->hu_faculty) == 'Fakulti Teknikal & Vokasional' ? 'selected' : '' }}>Fakulti Teknikal & Vokasional</option>
                                </select>
                                @error('faculty')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Course / Program</label>
                                <input type="text" name="course" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm  text-gray-700"
                                    value="{{ old('course', auth()->user()->hu_course) }}" />
                                @error('course')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Skills & Expertise</label>
                            <p class="text-xs text-gray-500 mb-3">List your main skill and select your proficiency level.</p>
                        </div>
                        
                        <div>
                            <input type="text" name="skills" 
                                class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 py-3 px-4 text-gray-700"
                                placeholder="e.g. Graphic Design, Tutoring" 
                                value="{{ old('skills', auth()->user()->hu_skills) }}" />
                            @error('skills')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                        <div class="flex items-center gap-2 mb-4">
                            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <h3 class="font-semibold text-gray-900">Work Experience (Optional)</h3>
                        </div>
                        
                        <div class="space-y-4">
                            <div>
                                <textarea id="work_experience_message" name="work_experience_message" rows="3" 
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                    placeholder="Briefly describe your past work experience...">{{ old('work_experience_message', auth()->user()->hu_work_experience_message ?? '') }}</textarea>
                                @error('work_experience_message')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Resume/CV</label>
                                <input type="file" id="work_experience_file" name="work_experience_file" 
                                    accept=".pdf,.doc,.docx"
                                    class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-xs file:font-semibold
                                      file:bg-indigo-50 file:text-indigo-700
                                      hover:file:bg-indigo-100 transition-all cursor-pointer"/>
                                <p class="text-xs text-gray-500 mt-2">PDF, DOC, DOCX up to 4MB.</p>
                                @error('work_experience_file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-6 border-t border-gray-100">
                        <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-900 transition-colors">
                            Cancel
                        </a>
                        <button type="submit" class="inline-flex justify-center items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform hover:-translate-y-0.5">
                            Save & Continue
                            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Profile Photo Preview Logic
        document.getElementById('profile_photo_input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('profile-photo-preview').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

    @if (session('ready_to_help'))
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    title: 'Profile Updated!',
                    text: 'You are now ready to help others.',
                    icon: 'success',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    backdrop: `rgba(0,0,123,0.4) left top no-repeat`,
                    willClose: () => {
                        window.location.href = "{{ route('services.create') }}";
                    }
                });
            });
        </script>
    @endif
</x-app-layout>