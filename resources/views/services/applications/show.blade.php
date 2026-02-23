<x-app-layout>
    <div class="py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('services.applications.index') }}" class="inline-flex items-center text-upsi-blue hover:text-upsi-blue/80 font-semibold">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back to Applications
                </a>
            </div>

            <!-- Application Details Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Header -->
                <div class="bg-upsi-blue bg-gradient-to-r from-upsi-blue to-upsi-blue/90 px-8 py-6 text-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <h1 class="text-2xl font-bold mb-2">{{ $application->service->title ?? $application->title }}</h1>
                            <p class="text-white/90">Applied {{ $application->created_at->format('F j, Y \a\t g:i A') }}</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            @if(auth()->user()->role === 'community' && auth()->user()->id === $application->user_id && !$application->service && $application->status === 'open')
                                <a href="{{ route('services.applications.edit', $application) }}" class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold bg-white/20 hover:bg-white/30 text-white transition-colors">
                                    Edit Details
                                </a>
                            @endif
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-semibold {{ $application->status_color }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-8 space-y-6">
                    <!-- Side-by-side: Service vs Request Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Service Information -->
                        @if($application->service)
                            <div>
                                <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Service Details</h2>
                                <div class="bg-upsi-light-gray rounded-xl p-6 space-y-6">
                                    <!-- Title -->
                                    <div>
                                        <h3 class="font-semibold text-upsi-text-primary">{{ $application->service->title }}</h3>
                                    </div>

                                    <!-- Summary cards aligned with Request Details -->
                                    @php
                                        $svc = $application->service;
                                        $svcType = $svc->category ? $svc->category->name : 'General';
                                        $svcPrice = $svc->price_range ?? ($svc->suggested_price !== null ? 'RM ' . number_format($svc->suggested_price, 2) : 'Negotiable');
                                        $svcStatus = $svc->status ?? ($svc->isAvailable() ? 'available' : 'busy');
                                        $svcStatusLabel = ucfirst(str_replace('_',' ', $svcStatus));
                                    @endphp
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Service Type</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $svcType }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.343-4 3s1.79 3 4 3 4-1.343 4-3M5 19h14" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Price</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $svcPrice }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Availability</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $svcStatusLabel }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    @if(!empty($application->service->description))
                                        <div>
                                            <p class="text-sm text-upsi-text-primary/60 mb-2">Description</p>
                                            <p class="text-upsi-text-primary whitespace-pre-line break-words">{{ $application->service->description }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Request / Application Details -->
                        <div>
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Request Details</h2>
                            <div class="bg-upsi-light-gray rounded-xl p-6 space-y-6">
                                @if(!$application->service)
                                    <!-- Original custom-request summary cards -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Service Type</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $application->formatted_service_type }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-2.21 0-4 1.343-4 3s1.79 3 4 3 4-1.343 4-3M5 19h14" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Budget</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $application->formatted_budget }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Timeline</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $application->formatted_timeline }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div>
                                        <p class="text-sm text-upsi-text-primary/60 mb-2">Description</p>
                                        <p class="text-upsi-text-primary whitespace-pre-line break-words">{{ $application->description }}</p>
                                    </div>

                                    <!-- Preferred Contact Methods as chips -->
                                    @php
                                        $methodsRaw = $application->contact_methods;
                                        $methods = is_array($methodsRaw)
                                            ? $methodsRaw
                                            : (is_string($methodsRaw) ? (json_decode($methodsRaw, true) ?? []) : []);
                                    @endphp
                                    @if(!empty($methods))
                                        <div>
                                            <p class="text-sm text-upsi-text-primary/60 mb-2">Preferred Contact</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach($methods as $m)
                                                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white border border-gray-200 text-upsi-text-primary text-sm">
                                                        @switch($m)
                                                            @case('platform_chat')
                                                                <svg class="w-4 h-4 text-upsi-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15a4 4 0 01-4 4H7l-4 4V6a4 4 0 014-4h10a4 4 0 014 4v9z"/></svg>
                                                                @break
                                                            @case('email')
                                                                <svg class="w-4 h-4 text-upsi-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4h16v16H4z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7l8 6 8-6"/></svg>
                                                                @break
                                                            @case('phone')
                                                                <svg class="w-4 h-4 text-upsi-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3l2 4-2 2a12 12 0 006 6l2-2 4 2v3a2 2 0 01-2 2h-1C9.163 20 4 14.837 4 8V7a2 2 0 01-1-2z"/></svg>
                                                                @break
                                                            @default
                                                                <svg class="w-4 h-4 text-upsi-blue" viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10" stroke-width="2"/></svg>
                                                        @endswitch
                                                        <span>{{ $m === 'platform_chat' ? 'Platform chat' : ucfirst(str_replace('_',' ', $m)) }}</span>
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <!-- Application summary when linked to a service -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Status</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ ucfirst(str_replace('_', ' ', $application->status)) }}</p>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-start space-x-3">
                                            <svg class="w-5 h-5 text-upsi-blue flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 12h10M7 17h10" />
                                            </svg>
                                            <div>
                                                <p class="text-xs text-upsi-text-primary/60">Applied On</p>
                                                <p class="text-sm font-medium text-upsi-text-primary">{{ $application->created_at->format('F j, Y \a\t g:i A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(!empty($application->message))
                                    <div>
                                        <p class="text-sm text-upsi-text-primary/60 mb-2">Message</p>
                                        <p class="text-upsi-text-primary">{{ $application->message }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Parties Involved -->
                    <div>
                        <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Parties Involved</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Applicant -->
                            <div class="bg-upsi-light-gray rounded-xl p-4">
                                <p class="text-sm text-upsi-text-primary/60 mb-2">Applicant</p>
                                <div class="flex items-center space-x-3">
                                    <a href="{{ route('students.profile', $application->user) }}" class="block group/avatar">
                                        @if($application->user->profile_photo_path)
                                            <img src="{{ asset('storage/' . $application->user->profile_photo_path) }}" 
                                                 alt="{{ $application->user->name }}" 
                                                 class="w-12 h-12 rounded-full object-cover ring-1 ring-gray-200 group-hover/avatar:ring-indigo-300">
                                        @else
                                            <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                {{ substr($application->user->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                    <div>
                                        <a href="{{ route('students.profile', $application->user) }}" class="font-semibold text-upsi-blue hover:text-upsi-blue/80">
                                            {{ $application->user->name }}
                                        </a>
                                        <div class="flex items-center space-x-2">
                                            <div class="flex items-center">
                                                @php($applicantRating = round($application->user->average_rating ?? 0))
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $applicantRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs text-upsi-text-primary/60">{{ number_format($application->user->average_rating ?? 0, 1) }}</span>
                                        </div>
                                        <p class="text-xs text-upsi-text-primary/60">{{ $application->user->email }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Provider -->
                            @if($application->service && $application->service->user)
                                <div class="bg-upsi-light-gray rounded-xl p-4">
                                    <p class="text-sm text-upsi-text-primary/60 mb-2">Service Provider</p>
                                    <div class="flex items-center space-x-3">
                                        <a href="{{ route('students.profile', $application->service->user) }}" class="block group/avatar">
                                            @if($application->service->user->profile_photo_path)
                                                <img src="{{ asset('storage/' . $application->service->user->profile_photo_path) }}" 
                                                     alt="{{ $application->service->user->name }}" 
                                                     class="w-12 h-12 rounded-full object-cover ring-1 ring-gray-200 group-hover/avatar:ring-indigo-300">
                                            @else
                                                <div class="w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                                    {{ substr($application->service->user->name, 0, 1) }}
                                                </div>
                                            @endif
                                        </a>
                                        <div>
                                            <a href="{{ route('students.profile', $application->service->user) }}" class="font-semibold text-upsi-blue hover:text-upsi-blue/80">
                                                {{ $application->service->user->name }}
                                            </a>
                                            <div class="flex items-center space-x-2">
                                                <div class="flex items-center">
                                                    @php($providerRating = round($application->service->user->average_rating ?? 0))
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-4 h-4 {{ $i <= $providerRating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                        </svg>
                                                    @endfor
                                                </div>
                                                <span class="text-xs text-upsi-text-primary/60">{{ number_format($application->service->user->average_rating ?? 0, 1) }}</span>
                                            </div>
                                            <p class="text-xs text-upsi-text-primary/60">{{ $application->service->user->email }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Application Message (chat-linked applications) -->
                    @if(!empty($application->message))
                        <div>
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Application Message</h2>
                            <div class="bg-upsi-light-gray rounded-xl p-6">
                                <p class="text-upsi-text-primary">{{ $application->message }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Student Interest CTA for open custom applications -->
                    @if(!$application->service && $application->status === 'open' && auth()->user()->role === 'student')
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Express Interest</h2>
                            @php($myInterest = $application->interests()->where('student_id', auth()->id())->first())
                            <p class="text-sm text-upsi-text-primary/60 mb-4">
                                @if($myInterest)
                                    <span class="inline-flex items-center px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold mr-2">Submitted</span>
                                    Thanks for your interest — we’ll reach out if it’s a fit.
                                @else
                                    Let the community member know you're interested in this request.
                                @endif
                            </p>
                            <div class="space-y-3">
                                <textarea id="interestMessage" class="w-full rounded-xl border-gray-200 focus:ring-upsi-blue focus:border-upsi-blue" rows="3" placeholder="Optional message (e.g., your availability or approach)"></textarea>
                                <div class="flex items-center space-x-3">
                                    <button onclick="expressInterest()" class="px-6 py-3 bg-indigo-50 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-100 transition-all duration-200" @if($myInterest) disabled @endif>
                                        @if($myInterest) Submitted @else I'm Interested @endif
                                    </button>
                                    <button onclick="openInterestsModal()" class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all duration-200">My Interested Requests</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Candidate List for community owner -->
                    @if(!$application->service && $application->status === 'open' && auth()->user()->role === 'community' && auth()->user()->id === $application->user_id)
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Interested Candidates</h2>
                            @php($interests = $application->interests()->with('student')->orderByDesc('created_at')->get())
                            @if($interests->isEmpty())
                                <div class="bg-upsi-light-gray rounded-xl p-6">
                                    <p class="text-upsi-text-primary/60">No students have expressed interest yet. Share your request to get more visibility.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach($interests as $interest)
                                        <div class="bg-white rounded-xl border border-gray-100 p-4 flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <a href="{{ route('students.profile', $interest->student) }}" class="font-semibold text-upsi-blue hover:text-upsi-blue/80">{{ $interest->student->name }}</a>
                                                <span class="text-xs px-2 py-1 rounded-full {{ $interest->status === 'selected' ? 'bg-green-100 text-green-700' : ($interest->status === 'declined' ? 'bg-red-100 text-red-700' : 'bg-blue-100 text-blue-700') }}">
                                                    {{ ucfirst($interest->status) }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                @if($interest->status !== 'declined')
                                                    @if($interest->status !== 'selected')
                                                        <button onclick="selectCandidate({{ $interest->id }})" class="px-6 py-3 bg-indigo-50 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-100 transition-all duration-200">Select Candidate</button>
                                                    @endif
                                                    <button onclick="declineCandidate({{ $interest->id }})" class="px-6 py-3 bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-all duration-200">Decline</button>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @php($selected = $application->selectedInterest)
                                @if($selected && $application->status === 'open')
                                    <div class="mt-4">
                                        <button onclick="confirmSelected()" class="px-6 py-3 bg-upsi-blue text-white font-semibold rounded-xl hover:bg-upsi-blue/90 transition-all duration-200">Start with Selected Candidate</button>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    <!-- Actions -->
                    @if($application->service && $application->status === 'open' && auth()->user()->role === 'student' && auth()->user()->id === $application->service->user_id)
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Actions</h2>
                            <div class="flex items-center space-x-4">
                                <button onclick="acceptApplication()" 
                                        class="px-6 py-3 bg-upsi-blue text-white font-semibold rounded-xl hover:bg-upsi-blue/90 transition-all duration-200">
                                    Accept Application
                                </button>
                                <button onclick="rejectApplication()" 
                                        class="px-6 py-3 bg-red-50 text-red-600 font-semibold rounded-xl hover:bg-red-100 transition-all duration-200">
                                    Decline Application
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($application->status === 'in_progress')
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold text-upsi-text-primary mb-4">Mark as Completed</h2>
                            <p class="text-sm text-upsi-text-primary/60 mb-4">
                                Once you've completed this service, mark it as completed. 
                                @if(auth()->user()->id === $application->user_id)
                                    After both parties confirm completion, you can leave a review.
                                @else
                                    The applicant will also need to confirm completion.
                                @endif
                            </p>
                            <button onclick="markCompleted()" 
                                    class="px-6 py-3 bg-green-50 text-green-600 font-semibold rounded-xl hover:bg-green-100 transition-all duration-200">
                                Mark as Completed
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openInterestsModal() {
            const m = document.getElementById('interestsModal');
            if (m) m.classList.remove('hidden');
        }
        function closeInterestsModal() {
            const m = document.getElementById('interestsModal');
            if (m) m.classList.add('hidden');
        }
        function expressInterest() {
            const message = document.getElementById('interestMessage')?.value || '';
            fetch('{{ route("services.applications.interest", $application) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Interest recorded. The community member may contact you.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to record interest');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        // withdrawInterest removed: keep interest expression only

        function selectCandidate(interestId) {
            const message = prompt('Optional message to the candidate:', 'Hi! You were selected for my request. Shall we chat?');
            fetch(`/services/applications/{{ $application->id }}/interests/${interestId}/select`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Candidate selected. A chat request has been sent.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to select candidate');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function declineCandidate(interestId) {
            if (!confirm('Decline this candidate?')) return;
            fetch(`/services/applications/{{ $application->id }}/interests/${interestId}/decline`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Candidate declined.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to decline candidate');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function confirmSelected() {
            if (!confirm('Confirm selected candidate and create a Service Request?')) return;
            fetch('{{ route("services.applications.interests.confirm", $application) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else if (data.success) {
                    alert('Selection confirmed.');
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to confirm selection');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function acceptApplication() {
            if (!confirm('Accept this application? The service will be marked as in progress.')) return;

            fetch('{{ route("services.applications.accept", $application) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to accept application');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function rejectApplication() {
            if (!confirm('Decline this application? This cannot be undone.')) return;

            fetch('{{ route("services.applications.reject", $application) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.href = '{{ route("services.applications.index") }}';
                } else {
                    alert(data.message || 'Failed to decline application');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }

        function markCompleted() {
            if (!confirm('Mark this service as completed?')) return;

            fetch('{{ route("services.applications.mark-completed", $application) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert(data.message || 'Failed to mark as completed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred');
            });
        }
    </script>
    @endpush

    @if(auth()->user()->role === 'student')
    <!-- Interests Modal -->
    @php($myInterestsList = \App\Models\ServiceApplicationInterest::with(['application.user'])
        ->where('student_id', auth()->id())
        ->orderByDesc('created_at')
        ->limit(20)
        ->get())
    <div id="interestsModal" class="fixed inset-0 bg-black/30 backdrop-blur-sm z-50 hidden" aria-hidden="true">
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl border border-gray-200">
                <div class="flex items-center justify-between p-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-upsi-text-primary">My Interested Requests</h3>
                    <button onclick="closeInterestsModal()" class="p-2 rounded-lg hover:bg-gray-100" aria-label="Close">
                        <svg class="w-5 h-5 text-gray-600" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="p-4 max-h-[60vh] overflow-y-auto">
                    @if($myInterestsList->isEmpty())
                        <div class="bg-upsi-light-gray rounded-xl p-6 text-center">
                            <p class="text-upsi-text-primary/60">You haven’t expressed interest in any requests yet.</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($myInterestsList as $interest)
                                <div class="flex items-center justify-between bg-white border border-gray-100 rounded-xl p-4">
                                    <div>
                                        <p class="font-semibold text-upsi-blue">
                                            <a href="{{ route('services.applications.show', $interest->application) }}" class="hover:underline">
                                                {{ optional($interest->application->service)->title ?? $interest->application->title ?? 'Community Request' }}
                                            </a>
                                        </p>
                                        <p class="text-xs text-upsi-text-primary/60">By {{ $interest->application->user->name }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        @php($status = $interest->status)
                                        @if($status === 'selected')
                                            <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold">Selected</span>
                                        @elseif($status === 'interested')
                                            <span class="px-2 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-semibold">Submitted</span>
                                        @elseif($status === 'declined')
                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">Submitted</span>
                                        @else
                                            <span class="px-2 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">Submitted</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <p class="mt-4 text-xs text-upsi-text-primary/60">Thanks for your interest — we’ll reach out if it’s a fit.</p>
                    @endif
                </div>
                <div class="p-4 border-t border-gray-200 flex justify-end">
                    <button onclick="closeInterestsModal()" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-xl hover:bg-gray-200">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</x-app-layout>
