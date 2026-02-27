<x-app-layout>
    <div class="min-h-screen bg-gray-50 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <br><br>
            {{-- 1. PROFILE HEADER SECTION --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-10">
                {{-- Banner --}}
                <div class="h-48 md:h-64 relative overflow-hidden bg-slate-200">
                    <div class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-transform duration-1000 hover:scale-105"
                        style="background-image: url('{{ asset('images/bgprofile.png') }}');">
                    </div>

                    {{-- Subtle Overlay (Agar elemen putih lebih menyerlah) --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent"></div>
                </div>

                <div class="px-8 pb-8">
                    <div class="relative flex flex-col md:flex-row items-end -mt-16 mb-6">
                        {{-- Avatar --}}
                        <div class="relative group">
                            <div
                                class="w-32 h-32 md:w-44 md:h-44 rounded-full border-4 border-white overflow-hidden bg-white shadow-lg">
                                @if ($user->hu_profile_photo_path)
                                    <img src="{{ asset( $user->hu_profile_photo_path) }}"
                                        alt="{{ $user->hu_name }}"
                                        class="w-full h-full object-cover transition transform group-hover:scale-105">
                                @else
                                    <div
                                        class="w-full h-full bg-slate-800 flex items-center justify-center text-white font-bold text-5xl">
                                        {{ substr($user->hu_name, 0, 1) }}
                                    </div>
                                @endif
                            </div>

                            {{-- Verified Badge --}}
                            @if ($user->hu_trust_badge)
                                <div class="absolute bottom-3 right-3 bg-blue-500 text-white w-8 h-8 flex items-center justify-center rounded-full border-2 border-white shadow-sm"
                                    title="Verified Student">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        {{-- Name & Status --}}
                        <div class="md:ml-8 mt-6 md:mt-0 flex-1 w-full">
                            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                                <div>
                                    <h1 class="text-3xl font-bold text-gray-900 leading-tight mb-1">{{ $user->hu_name }}
                                    </h1>
                                    <div class="flex items-center gap-2 text-gray-600 font-medium mb-3">
                                        <i class="fa-solid fa-graduation-cap text-indigo-500"></i>
                                        {{ $user->hu_faculty ?? 'Faculty of Computing' }}
                                        <span class="text-gray-300 mx-1">•</span>
                                        <span class="text-sm text-gray-500">{{ $user->hu_course ?? 'Student' }}</span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4">
                                        {{-- Availability Badge --}}
                                        @if ($user->hu_is_available)
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200 shadow-sm">
                                                <span class="relative flex h-2 w-2">
                                                    <span
                                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                                    <span
                                                        class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                                </span>
                                                Available
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-500 border border-slate-200">
                                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>Unavailable
                                            </span>
                                        @endif

                                        {{-- Rating Badge --}}
                                        <div
                                            class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-yellow-50 border border-yellow-200 text-yellow-700 text-xs font-bold shadow-sm">
                                            <i class="fa-solid fa-star text-yellow-500"></i>
                                            {{ number_format($user->hu_average_rating ?? 0, 1) }}
                                            <span class="font-normal text-yellow-600 opacity-80">
                                                {{ $reviews->count() }}
                                                reviews</span>
                                        </div>
                                    </div>
                                </div>

                                {{-- ACTION BUTTONS --}}
                                <div class="mt-6 md:mt-0 flex flex-col sm:flex-row gap-3">
                                    @auth
                                        @if (auth()->id() !== $user->hu_id)
                                            @php
                                                // Prepare WhatsApp Link
                                                $rawPhone = $user->hu_phone_number ?? ($user->hu_phone ?? '');
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $rawPhone);
                                                if (substr($cleanPhone, 0, 1) === '0') {
                                                    $cleanPhone = '60' . substr($cleanPhone, 1);
                                                }
                                                $whatsappUrl =
                                                    "https://wa.me/{$cleanPhone}?text=Hi " .
                                                    urlencode($user->hu_name) .
                                                    ', I saw your profile on S2U.';
                                            @endphp

                                            @if (!empty($cleanPhone))
                                                <a href="{{ $whatsappUrl }}" target="_blank"
                                                    class="inline-flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl transition shadow-lg shadow-green-500/30 transform hover:-translate-y-0.5">
                                                    <i class="fa-brands fa-whatsapp text-xl mr-2"></i>
                                                    Chat on WhatsApp
                                                </a>
                                            @else
                                                <button disabled
                                                    class="inline-flex items-center justify-center px-6 py-3 bg-gray-300 text-gray-500 font-bold rounded-xl cursor-not-allowed">
                                                    No Phone Linked
                                                </button>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}"
                                            class="inline-flex items-center justify-center px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white font-bold rounded-xl shadow-lg transition">
                                            Login to Contact
                                        </a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Bio Section --}}
                    @if ($user->hu_bio)
                        <div class="mt-8 border-t border-gray-100 pt-6">
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">About Me</h3>
                            <p class="text-gray-700 leading-relaxed text-lg max-w-4xl font-light">{{ $user->hu_bio }}
                            </p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- 2. CONTENT GRID --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                {{-- LEFT SIDEBAR (Info, Skills, Education, Exp) --}}
                <div class="space-y-8">
                    {{-- Stats Card --}}
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-900 mb-4">Quick Stats</h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                                <span class="text-gray-500">Member Since</span>
                                <span class="font-semibold text-gray-900">{{ $user->hu_created_at->format('M Y') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm border-b border-gray-50 pb-2">
                                <span class="text-gray-500">Total Services</span>
                                <span class="font-semibold text-gray-900">{{ $services->count() }}</span>
                            </div>
                        </div>
                    </div>

                    @if ($user->hu_skills)
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-wand-magic-sparkles text-indigo-500"></i> Skills
                            </h3>
                            <div class="flex flex-wrap gap-2">
                                @foreach (explode(',', $user->hu_skills) as $skill)
                                    <span
                                        class="px-3 py-1 rounded-lg text-sm font-medium bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ trim($skill) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($user->hu_faculty || $user->hu_course)
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-university text-indigo-500"></i> Education
                            </h3>
                            <div class="space-y-4">
                                <div class="relative pl-4 border-l-2 border-indigo-200">
                                    <h4 class="font-semibold text-gray-900">{{ $user->hu_course ?? 'Course Not Set' }}
                                    </h4>
                                    <p class="text-sm text-gray-600">{{ $user->hu_faculty ?? 'Faculty Not Set' }}</p>
                                    <p class="text-xs text-gray-400 mt-1">Current Student</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Resume / Experience Section --}}
                    @if ($user->hu_work_experience_message || $user->hu_work_experience_file)
                        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                            <h3 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                                <i class="fa-solid fa-briefcase text-indigo-500"></i> Experience
                            </h3>

                            @if ($user->hu_work_experience_message)
                                <div
                                    class="text-sm text-gray-600 leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100 mb-4">
                                    {!! nl2br(e($user->hu_work_experience_message)) !!}
                                </div>
                            @endif

                            @if ($user->hu_work_experience_file)
                                <a href="{{ asset('storage/' . $user->hu_work_experience_file) }}" target="_blank"
                                    class="flex items-center justify-center w-full px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition shadow-sm group">
                                    <i
                                        class="fa-regular fa-file-pdf text-red-500 mr-2 group-hover:scale-110 transition-transform"></i>
                                    View Resume / CV
                                </a>
                            @endif
                        </div>
                    @endif
                </div>

                {{-- RIGHT CONTENT (Services & Reviews) --}}
                <div class="lg:col-span-2 space-y-10">

                    {{-- 🟢 SERVICES OFFERED SECTION --}}
                    <div id="services-section">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-2xl font-bold text-slate-900">Services Offered</h2>
                            <span
                                class="bg-indigo-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm shadow-indigo-200">
                                {{ $services->count() }} Active
                            </span>
                        </div>

                        @if ($services->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach ($services as $service)
                                    <div
                                        class="bg-white rounded-2xl shadow-sm border border-gray-100 hover:shadow-lg transition-all group overflow-hidden flex flex-col h-full hover:-translate-y-1 duration-300">

                                        {{-- Service Image --}}
                                       <a href="{{ route('services.details', $service) }}" class="block h-48 bg-gray-100 overflow-hidden relative">
    @if ($service->hss_image_path)
        @php
            $path = $service->hss_image_path;
            
            // 1. Check if it's an external URL (e.g. placeholder or S3)
            if (Str::startsWith($path, ['http://', 'https://'])) {
                $imageUrl = $path;
            } 
            // 2. Check if file exists in the 'storage' folder (public/storage/...)
            elseif (file_exists(public_path('storage/' . $path))) {
                $imageUrl = asset('storage/' . $path);
            } 
            // 3. Fallback: Assume it's in the public root (public/...)
            else {
                $imageUrl = asset($path);
            }
        @endphp

        <img src="{{ $imageUrl }}" 
             alt="{{ $service->hss_title }}"
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
    @else
        <div class="w-full h-full flex items-center justify-center bg-slate-50 text-slate-300">
            <i class="fa-regular fa-image text-4xl"></i>
        </div>
    @endif

    {{-- Price Tag --}}
    <div class="absolute bottom-3 right-3 bg-slate-900/90 backdrop-blur-md text-white px-3 py-1.5 rounded-lg text-sm font-bold shadow-lg">
        RM {{ number_format($service->hss_basic_price ?? 0, 0) }}
    </div>
</a>                                        {{-- Service Details --}}
                                        <div class="p-5 flex flex-col flex-1">
                                            <div class="mb-2">
                                                <span
                                                    class="text-xs font-bold text-indigo-600 uppercase tracking-wider">
                                                    {{ $service->category->hc_name ?? 'Service' }}
                                                </span>
                                            </div>

                                        <a href="{{ route('services.details', $service) }}"
                                                class="text-lg font-bold text-gray-900 hover:text-indigo-600 line-clamp-2 leading-tight mb-2">
                                                {{ $service->hss_title }}
                                            </a>

                                            <p class="text-sm text-gray-500 line-clamp-2 mb-4 flex-1">
                                                {{ Str::limit(strip_tags($service->hss_description), 80) }}
                                            </p>


                                            <div
                                                class="pt-4 border-t border-gray-50 flex items-center justify-between">
                                                <div
                                                    class="flex items-center gap-1.5 text-xs text-slate-500 font-medium bg-slate-50 px-2 py-1 rounded-lg border border-slate-100">
                                                    {{-- Ikon Bintang --}}
                                                    <i class="fa-solid fa-star text-amber-400"></i>

                                                    {{-- Papar rating: jika tiada ulasan, ia akan papar 0.0 --}}
                                                    <span class="text-slate-900 font-bold">
                                                        {{ number_format($service->reviews_avg_rating ?? 0, 1) }}
                                                    </span>

                                                    {{-- Optional: Papar jumlah ulasan untuk servis tersebut --}}
                                                    <span class="opacity-50 text-[10px]">
                                                        ({{ $service->reviews_count ?? 0 }})
                                                    </span>
                                                </div>
                                                <a href="{{ route('services.details', $service) }}"
                                                    class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 group-hover:gap-2 transition-all">
                                                    View Details <i class="fa-solid fa-arrow-right text-xs"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty Services State --}}
                            <div class="bg-white rounded-xl border border-dashed border-gray-300 p-10 text-center">
                                <div
                                    class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-300">
                                    <i class="fa-solid fa-box-open text-2xl"></i>
                                </div>
                                <p class="text-gray-500 font-medium">No services listed yet.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 🟢 REVIEWS SECTION --}}
                    <div id="reviews-section"
                        class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-gray-100 font-sans">
                        {{-- Review Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-10">
                            <div>
                                <h2 class="text-xl font-bold text-gray-900">Reviews & Ratings</h2>
                                <p class="text-sm text-gray-500">Feedback from previous clients</p>
                            </div>

                            <div
                                class="flex items-center gap-4 bg-slate-50 px-5 py-3 rounded-2xl border border-slate-100">
                                <span
                                    class="text-3xl font-bold text-slate-900">{{ number_format($user->hu_average_rating ?? 0, 1) }}</span>
                                <div class="h-8 w-px bg-slate-200"></div>
                                <div class="flex flex-col">
                                    {{-- 1. Bintang Rating --}}
                                    <div class="flex text-yellow-400 text-xs mb-0.5">
                                        @for ($i = 0; $i < 5; $i++)
                                            {{-- Menggunakan average_rating yang kita kira di controller --}}
                                            <i
                                                class="fas fa-star {{ $i < round($user->hu_average_rating ?? 0) ? '' : 'text-gray-200' }}"></i>
                                        @endfor
                                    </div>

                                    {{-- 2. Jumlah Ulasan --}}
                                    <span class="text-[11px] text-gray-500 font-semibold">
                                        {{-- Kita kira terus dari collection reviews yang di-load --}}
                                        {{ $reviews->count() }} Total Reviews
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Reviews List --}}
                        @if (isset($reviews) && $reviews->count() > 0)
                            <div class="space-y-10">
                                @foreach ($reviews as $review)
                                    <div class="relative">
                                        <div class="flex items-start gap-4">
                                          {{-- Reviewer Avatar --}}
<div class="flex-shrink-0">
    @if ($review->reviewer && $review->reviewer->hu_profile_photo_path)
        {{-- Display Image --}}
        <img src="{{ asset($review->reviewer->hu_profile_photo_path) }}" 
             alt="{{ $review->reviewer->hu_name }}"
             class="w-11 h-11 rounded-full object-cover border border-indigo-100 shadow-sm">
    @else
        {{-- Fallback to Initials --}}
        <div class="w-11 h-11 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold border border-indigo-100 shadow-sm">
            {{ substr($review->reviewer->hu_name ?? 'A', 0, 1) }}
        </div>
    @endif
</div>
                                            <div class="flex-1">
                                                <div
                                                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                                    <div>
                                                        <h4 class="font-bold text-gray-900 text-sm">
                                                            {{ $review->reviewer->hu_name ?? 'Anonymous User' }}</h4>
                                                        <div class="flex items-center gap-2 mt-0.5">
                                                            <div class="flex text-yellow-400 text-[10px]">
                                                                @for ($i = 1; $i <= 5; $i++)
                                                                    <i
                                                                        class="fas fa-star {{ $i <= $review->hr_rating ? '' : 'text-gray-200' }}"></i>
                                                                        class="fas fa-star {{ $i <= $review->hr_rating ? '' : 'text-gray-200' }}"></i>
                                                                @endfor
                                                            </div>
                                                            <span class="text-[11px] text-gray-400">•
                                                                {{ optional($review->hr_created_at)->diffForHumans() ?? 'Recently' }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- 🏷️ Service Reference Badge --}}
                                                @if ($review->studentService)
                                                    <div class="mb-3">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-blue-50 text-blue-700 border border-blue-100">
                                                            <i class="fa-solid fa-tag mr-1.5 opacity-60"></i>
                                                            Service: {{ $review->studentService->hss_title }}
                                                        </span>
                                                    </div>
                                                @endif

                                                {{-- Review Comment --}}
                                                @if ($review->hr_comment)
                                                    <div
                                                        class="text-sm text-gray-600 leading-relaxed bg-gray-50/50 p-4 rounded-xl rounded-tl-none border border-gray-100">
                                                        {{ $review->hr_comment }}
                                                    </div>
                                                @endif

                                                {{-- 💬 Seller Reply (Dinamik dari DB anda) --}}
                                                @if ($review->hr_reply)
                                                    <div class="mt-4 ml-4 md:ml-8 relative">
                                                        {{-- Garis penunjuk balasan --}}
                                                        <div
                                                            class="absolute -left-4 top-0 bottom-0 w-0.5 bg-gray-100 rounded-full">
                                                        </div>

                                                        <div
                                                            class="bg-indigo-50/30 border border-indigo-50 p-4 rounded-xl">
                                                            <div class="flex items-center gap-2 mb-1.5">
                                                                <span
                                                                    class="text-[10px] font-bold text-indigo-600 uppercase tracking-wider">Seller's
                                                                    Response</span>
                                                                @if ($review->hr_replied_at)
                                                                    <span class="text-[10px] text-gray-400">•
                                                                        {{ \Carbon\Carbon::parse($review->hr_replied_at)->diffForHumans() }}</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-sm text-gray-700 italic leading-relaxed">
                                                                "{{ $review->hr_reply }}"
                                                            </p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if (!$loop->last)
                                        <hr class="border-gray-50 mt-8">
                                    @endif
                                @endforeach
                            </div>
                        @else
                            {{-- Empty Reviews State --}}
                            <div class="text-center py-12">
                                <div
                                    class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 text-gray-300 mb-4">
                                    <i class="fa-regular fa-comment-dots text-2xl"></i>
                                </div>
                                <h3 class="text-gray-900 font-bold">No reviews yet</h3>
                                <p class="text-gray-500 text-sm mt-1">Be the first to hire and review
                                    {{ $user->hu_name }}!</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
