<x-app-layout>
    <style>
        /* Smooth removal animation */
        .card-removed {
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.4s ease;
        }
    </style>

    <div class="min-h-screen bg-gray-50 pt-4 pb-12 px-4 sm:px-6 lg:px-8">
        <main class="max-w-7xl mx-auto">

            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 tracking-tight">My Saved Services</h1>
                    <p class="mt-1 text-sm text-gray-500">Access and view your list of favourite services.</p>
                </div>
                <a href="{{ route('services.index') }}"
                    class="text-indigo-600 font-bold hover:text-indigo-700 flex items-center gap-2 transition-all">
                    Find more services <i class="fa-solid fa-arrow-right text-sm"></i>
                </a>
            </div>

            @if ($favourites->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach ($favourites as $service)
                        {{-- START NEW CARD DESIGN --}}
                        <div class="service-card group bg-white rounded-2xl border border-gray-200 hover:border-indigo-100 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden relative">

                            {{-- Image Section --}}
                            <div class="relative h-56 bg-gray-200 overflow-hidden block">
                                <a href="{{ route('services.details', $service->hss_id) }}">
                                    <img src="{{ $service->ui_image_url }}"
                                        data-fallback-src="{{ $service->ui_image_fallback }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </a>

                                {{-- Category Badge --}}
                                @if ($service->category)
                                    <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                                        style="color: {{ $service->category->hc_color }}">
                                        {{ $service->category->hc_name }}
                                    </span>
                                @endif

                                {{-- REMOVE BUTTON (Integrated into new design) --}}
                                <button data-favorite-remove data-service-id="{{ $service->hss_id }}"
                                    class="absolute top-4 right-4 bg-white/95 text-red-500 w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:bg-red-500 hover:text-white transition-all transform active:scale-90"
                                    title="Remove from favorites">
                                    <i class="fa-solid fa-heart"></i>
                                </button>
                            </div>

                            {{-- Content Section --}}
                            <div class="p-5 flex flex-col flex-1">
                                {{-- User Info & Rating Row --}}
                                <div class="flex items-center gap-3 mb-3">
                                    <img src="{{ $service->ui_seller_avatar_url }}"
                                        data-fallback-src="{{ $service->ui_seller_avatar_fallback }}"
                                        class="w-8 h-8 rounded-full object-cover border border-slate-100">
                                    
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-gray-900 flex items-center gap-1">
                                            {{ Str::limit($service->user->hu_name, 15) }}
                                            @if ($service->user->hu_trust_badge)
                                                <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                                            @endif
                                        </span>
                                        <span class="text-[10px] text-gray-500">Student seller</span>
                                    </div>

                                    {{-- Rating Badge --}}
                                    <div class="ml-auto flex items-center gap-1 bg-gray-50 px-2 py-1 rounded text-xs">
                                        <i class="fas fa-star text-yellow-400"></i>
                                        <span class="font-bold text-gray-700">
                                            {{ number_format($service->reviews_avg_rating ?? 0, 1) }}
                                        </span>
                                        <span class="text-gray-400">
                                            ({{ $service->reviews_count ?? 0 }})
                                        </span>
                                    </div>
                                </div>

                                {{-- Title --}}
                                <a href="{{ route('services.details', $service->hss_id) }}" class="block mb-2">
                                    <h3 class="text-lg font-bold text-gray-900 group-hover:text-indigo-600 transition-colors line-clamp-2 leading-tight">
                                        {{ $service->hss_title }}
                                    </h3>
                                </a>

                                {{-- Description --}}
                                <div class="text-sm text-gray-500 line-clamp-2 mb-4">
                                    {{ Str::limit(strip_tags($service->hss_description), 80) }}
                                </div>

                                {{-- Footer: Price & Button --}}
                                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                    <div>
                                        <span class="text-xs text-gray-400 font-medium uppercase">Starting at</span>
                                        <div class="text-lg font-bold text-gray-900">
                                            RM{{ number_format($service->hss_basic_price, 0) }}
                                        </div>
                                    </div>
                                    <a href="{{ route('services.details', $service->hss_id) }}"
                                        class="px-4 py-2 bg-slate-900 hover:bg-indigo-600 text-white text-sm font-semibold rounded-lg transition-colors shadow-md">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- END NEW CARD DESIGN --}}
                    @endforeach
                </div>

                <div class="mt-12">
                    {{ $favourites->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-24 bg-white rounded-[3rem] border border-dashed border-slate-200">
                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 text-slate-300">
                        <i class="fa-regular fa-heart text-4xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-slate-900">Your wishlist is empty</h3>
                    <p class="text-slate-500 mt-2 mb-8">Save services you're interested in to see them here.</p>
                    <a href="{{ route('services.index') }}"
                        class="px-8 py-3 bg-indigo-600 text-white font-bold rounded-2xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-100">
                        Explore Services
                    </a>
                </div>
            @endif
        </main>
    </div>

    <div id="favoritesIndexConfig"
        data-toggle-url="{{ route('favorites.services.toggle') }}"
        data-csrf-token="{{ csrf_token() }}"></div>
    <script src="{{ asset('js/favorites-index.js') }}"></script>
</x-app-layout>
