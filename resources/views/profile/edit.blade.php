<x-app-layout>
    {{-- Load FontAwesome if not already loaded in layout --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <div class="min-h-screen bg-slate-50/50 py-16 font-sans">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            
            {{-- HEADER SECTION --}}
            <div class="mb-10 text-center md:text-left">
               
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-black text-slate-900 tracking-tight">Profile Settings</h1>
                        <p class="mt-2 text-slate-500 font-medium">Manage your identity, security, and reputation.</p>
                    </div>
                    
                    {{-- Quick Stat Badge --}}
                    <div class="hidden md:flex items-center gap-3 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-100">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-50 text-yellow-500">
                            <i class="fas fa-star"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400 font-bold uppercase tracking-wider">Rating</p>
                            <p class="text-lg font-black text-slate-900">{{ $profileEditUi['rating_display'] }} <span class="text-xs text-slate-400 font-medium">/ 5.0</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex flex-col lg:flex-row gap-8 items-start" id="profileEditRoot">
                
                {{-- SIDEBAR NAVIGATION --}}
                <aside class="lg:w-72 flex-shrink-0 w-full sticky top-24">
                    <div class="bg-white p-3 rounded-[2rem] shadow-sm border border-slate-200/60">
                        <nav class="space-y-1">
                            {{-- Profile Tab --}}
                            <button type="button"
                                data-profile-tab-button="profile"
                                class="flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all duration-300 w-full group {{ $profileEditUi['initial_tab'] === 'profile' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                data-active-class="bg-indigo-600 text-white shadow-lg shadow-indigo-100"
                                data-inactive-class="text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="mr-3 h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Profile Details
                            </button>

                            {{-- Reviews Tab (NEW) --}}
                            <button type="button"
                                data-profile-tab-button="reviews"
                                class="flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all duration-300 w-full group {{ $profileEditUi['initial_tab'] === 'reviews' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                data-active-class="bg-indigo-600 text-white shadow-lg shadow-indigo-100"
                                data-inactive-class="text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="mr-3 h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                </svg>
                                My Reviews
                                <span class="ml-auto bg-slate-100 text-slate-600 py-0.5 px-2 rounded-full text-[10px]">{{ $totalReviews ?? 0 }}</span>
                            </button>

                            {{-- Password Tab --}}
                            <button type="button"
                                data-profile-tab-button="password"
                                class="flex items-center px-5 py-4 text-sm font-bold rounded-2xl transition-all duration-300 w-full group {{ $profileEditUi['initial_tab'] === 'password' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-100' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                data-active-class="bg-indigo-600 text-white shadow-lg shadow-indigo-100"
                                data-inactive-class="text-slate-500 hover:bg-slate-50 hover:text-slate-900">
                                <svg class="mr-3 h-5 w-5 transition-transform group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                                Password & Security
                            </button>
                        </nav>
                        
                        {{-- Quick Help Box --}}
                        <div class="mt-4 p-5 bg-slate-50 rounded-[1.5rem] border border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Need Help?</p>
                            <p class="text-xs text-slate-500 leading-relaxed">Contact U-Serve support if you're having trouble changing your credentials.</p>
                        </div>
                    </div>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <div class="flex-1 w-full">
                    
                    {{-- TAB 1: PROFILE INFORMATION --}}
                    <div data-profile-tab-panel="profile"
                         class="{{ $profileEditUi['initial_tab'] === 'profile' ? '' : 'hidden' }} bg-white shadow-sm rounded-[2.5rem] border border-slate-200/60 overflow-hidden">
                        
                        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div>
                                <h3 class="text-xl font-black text-slate-900 tracking-tight">Profile Information</h3>
                                <p class="text-sm text-slate-500 font-medium">Update your public identity and contact info.</p>
                            </div>
                        </div>
                        
                        <div class="p-8 md:p-10">
                            <form method="post" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="space-y-10">
                                @csrf
                                @method('patch')

                                {{-- Photo Upload UI --}}
                                <div class="flex flex-col sm:flex-row items-center gap-8 pb-8 border-b border-slate-50">

    <div class="relative group">
        <div class="w-28 h-28 md:w-32 md:h-32 rounded-[2rem] overflow-hidden border-4 border-white shadow-2xl bg-slate-100 ring-1 ring-slate-200">

            {{-- ?? Preview Image (shows after user selects new file) --}}
            <img id="profile-photo-preview"
                 src=""
                 class="w-full h-full object-cover hidden">

            {{-- ?? Existing profile image --}}
            @if($profileEditUi['profile_photo_url'])
                <img id="profile-photo-current"
                     src="{{ $profileEditUi['profile_photo_url'] }}"
                     class="w-full h-full object-cover">
            @else
                {{-- ?? Fallback letter avatar --}}
                <div id="profile-photo-fallback"
                     class="w-full h-full flex items-center justify-center bg-indigo-600 text-white text-4xl font-black">
                    {{ $profileEditUi['profile_initial'] }}
                </div>
            @endif

        </div>

        {{-- Upload button --}}
        <label for="profile_photo"
               class="absolute -bottom-2 -right-2 p-3 bg-white rounded-2xl shadow-xl border border-slate-100 cursor-pointer hover:text-indigo-600 text-slate-400 transition-all transform hover:scale-110 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                      d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
        </label>

        {{-- Hidden file input --}}
        <input id="profile_photo"
               name="profile_photo"
               type="file"
               accept="image/*"
               class="hidden">
    </div>

    <div class="text-center sm:text-left">
        <h4 class="text-lg font-bold text-slate-900">Your Photo</h4>
        <p class="text-sm text-slate-400 mt-1 max-w-[200px]">We support JPG, PNG or GIF. Maximum file size is 1MB.</p>
        @error('profile_photo')
            <p class="text-xs text-rose-500 font-bold mt-2">{{ $message }}</p>
        @enderror
    </div>

</div>

                                {{-- Input Fields Grid --}}
                                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Full Name</label>
                                        <input type="text" name="name" value="{{ old('name', $user->hu_name) }}" required 
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                        @error('name') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                                        <input type="email" name="email" value="{{ old('email', $user->hu_email) }}" required
                                            @if(!$profileEditUi['can_edit_email']) readonly @endif
                                            class="block w-full rounded-2xl border-slate-200 py-3.5 px-4 transition-all font-semibold 
                                            @if(!$profileEditUi['can_edit_email']) bg-slate-100 text-slate-400 cursor-not-allowed @else bg-slate-50/50 text-slate-700 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 @endif">
                                        @error('email') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Phone Number</label>
                                        <input type="text" name="phone" value="{{ old('phone', $user->hu_phone) }}" placeholder="+60..."
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                    </div>

                                    @if($user->hu_role === 'student')
                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Student ID</label>
                                        <input type="text" value="{{ $user->hu_student_id }}" readonly
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-100 py-3.5 px-4 text-slate-400 font-bold cursor-not-allowed">
                                    </div>

                                    <div class="sm:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-8">
                                        <div>
                                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Faculty</label>
                                            <input type="text" name="faculty" value="{{ old('faculty', $user->hu_faculty) }}"
                                                class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Course / Program</label>
                                            <input type="text" name="course" value="{{ old('course', $user->hu_course) }}"
                                                class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold text-slate-700">
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Footer Actions --}}
                                <div class="pt-10 flex items-center justify-end border-t border-slate-50 gap-6">
                                    @if ($profileEditUi['profile_updated'])
                                        <span data-profile-autohide class="text-sm text-emerald-600 font-bold flex items-center">
                                            <i class="fa-solid fa-circle-check mr-2"></i> Changes Saved
                                        </span>
                                    @endif
                                    <button type="submit" class="px-10 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 hover:shadow-indigo-100 active:scale-95">
                                        Save Profile
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- TAB 2: REVIEWS (NEW - Carousell Style) --}}
                    <div data-profile-tab-panel="reviews"
                         class="{{ $profileEditUi['initial_tab'] === 'reviews' ? '' : 'hidden' }} bg-white shadow-sm rounded-[2.5rem] border border-slate-200/60 overflow-hidden">
                        
                        {{-- Review Header Summary --}}
                        <div class="px-8 py-8 border-b border-slate-100 bg-slate-50/50">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 tracking-tight">Sellers Feedback</h3>
                                    <p class="text-sm text-slate-500 font-medium">Feedback from sellers regarding their interactions with you.</p>
                                </div>
                                
                                {{-- Rating Big Badge --}}
                                @if($profileEditUi['has_reviews'])
                                <div class="flex items-center gap-4 bg-white px-5 py-3 rounded-2xl shadow-sm border border-slate-200">
                                    <div class="text-3xl font-black text-indigo-600">{{ $profileEditUi['rating_display'] }}</div>
                                    <div class="h-8 w-px bg-slate-200"></div>
                                    <div class="flex flex-col">
                                        <div class="flex text-yellow-400 text-xs mb-1">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="{{ $i <= $profileEditUi['rating_stars_filled'] ? 'fas' : 'far' }} fa-star"></i>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">{{ $totalReviews ?? 0 }} Reviews</span>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        {{-- Review List --}}
                        <div class="divide-y divide-slate-100">
                            @if($profileEditUi['has_reviews'])
                                @foreach($reviewsView as $review)
                                <div class="p-8 hover:bg-slate-50/30 transition-colors">
                                    <div class="flex items-start gap-4 md:gap-6">
                                        {{-- Avatar --}}
                                        <div class="flex-shrink-0">
                                            @if($review['reviewer_photo_url'])
                                                <img src="{{ $review['reviewer_photo_url'] }}" 
                                                     class="w-12 h-12 md:w-14 md:h-14 rounded-full object-cover shadow-sm border-2 border-white ring-1 ring-slate-100">
                                            @else
                                                <div class="w-12 h-12 md:w-14 md:h-14 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-lg shadow-sm border-2 border-white ring-1 ring-slate-100">
                                                    {{ $review['reviewer_initial'] }}
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Content --}}
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-2">
                                                <h4 class="text-base font-bold text-slate-900 truncate">
                                                    {{ $review['reviewer_name'] }}
                                                </h4>
                                                <span class="text-xs font-medium text-slate-400 mt-1 sm:mt-0">
                                                    {{ $review['created_human'] }}
                                                </span>
                                            </div>

                                            {{-- Stars --}}
                                            <div class="flex text-yellow-400 text-xs mb-3">
                                                @for($i=1; $i<=5; $i++)
                                                    <i class="{{ $i <= $review['rating'] ? 'fas' : 'far' }} fa-star"></i>
                                                @endfor
                                            </div>

                                            {{-- Comment --}}
                                            @if($review['has_comment'])
                                                <p class="text-slate-600 text-sm leading-relaxed mb-3">
                                                    {{ $review['comment'] }}
                                                </p>
                                            @else
                                                <p class="text-slate-400 text-sm italic mb-3">No written review provided.</p>
                                            @endif

                                            {{-- Context Badge (Carousell Style) --}}
                                            @if($review['has_service'])
                                                <div class="inline-flex items-center gap-2 bg-slate-100 px-3 py-1.5 rounded-lg">
                                                    <div class="w-6 h-6 rounded bg-white flex items-center justify-center border border-slate-200">
                                                        <i class="fas fa-shopping-bag text-[10px] text-slate-400"></i>
                                                    </div>
                                                    <div class="flex flex-col">
                                                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider leading-none">You bought</span>
                                                        <span class="text-xs font-bold text-slate-700 leading-none mt-1">{{ $review['service_title_short'] }}</span>
                                                    </div>
                                                </div>
                                            @endif

                                            {{-- Reply Section --}}
                                            @if($review['has_reply'])
                                                <div class="mt-4 bg-indigo-50/50 border border-indigo-100 rounded-xl p-4 ml-0 md:ml-4">
                                                    <p class="text-xs font-bold text-indigo-900 mb-1 flex items-center gap-1">
                                                        <i class="fas fa-reply fa-rotate-180"></i> Your Reply:
                                                    </p>
                                                    <p class="text-sm text-indigo-800/80 italic">"{{ $review['reply'] }}"</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="p-16 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                                        <i class="fa-regular fa-star text-3xl text-slate-300"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-slate-900">No reviews yet</h3>
                                    <p class="text-slate-500 text-sm mt-1">Reviews will appear here once you complete services.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- TAB 3: PASSWORD UPDATE --}}
                    <div data-profile-tab-panel="password"
                         class="{{ $profileEditUi['initial_tab'] === 'password' ? '' : 'hidden' }} bg-white shadow-sm rounded-[2.5rem] border border-slate-200/60 overflow-hidden">
                        
                        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                            <h3 class="text-xl font-black text-slate-900 tracking-tight">Security Credentials</h3>
                            <p class="text-sm text-slate-500 font-medium">Keep your account safe by using a strong password.</p>
                        </div>
                        
                        <div class="p-8 md:p-10">
                            <form method="post" action="{{ route('password.update') }}" class="space-y-8 max-w-2xl">
                                @csrf
                                @method('put')

                                <div>
                                    <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Current Password</label>
                                    <input type="password" name="current_password" 
                                        class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold">
                                    @error('current_password', 'updatePassword') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">New Password</label>
                                        <input type="password" name="password" 
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold">
                                        @error('password', 'updatePassword') <p class="mt-2 text-xs text-rose-500 font-bold">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2 ml-1">Confirm New Password</label>
                                        <input type="password" name="password_confirmation" 
                                            class="block w-full rounded-2xl border-slate-200 bg-slate-50/50 py-3.5 px-4 focus:bg-white focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition-all font-semibold">
                                    </div>
                                </div>

                                <div class="pt-8 flex items-center justify-end border-t border-slate-50 gap-6">
                                    @if ($profileEditUi['password_updated'])
                                        <span data-profile-autohide class="text-sm text-emerald-600 font-bold flex items-center">
                                            <i class="fa-solid fa-shield-check mr-2"></i> Security Updated
                                        </span>
                                    @endif
                                    <button type="submit" class="px-10 py-4 bg-slate-900 text-white font-bold rounded-2xl hover:bg-indigo-600 transition-all shadow-xl shadow-slate-200 hover:shadow-indigo-100 active:scale-95">
                                        Update Password
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <div id="profileEditConfig"
        data-initial-tab="{{ $profileEditUi['initial_tab'] }}"
        data-autohide-ms="3000"></div>
    <script src="{{ asset('js/nonadmin-profile-edit.js') }}"></script>
</x-app-layout>
