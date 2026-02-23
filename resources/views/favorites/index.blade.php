<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Favourite | Upsi Service Circle</title>
    <link rel="icon" type="image/png" href="/images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@600;700;800&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        h1, h2, h3 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* Smooth removal animation */
        .card-removed {
            transform: scale(0.9);
            opacity: 0;
            transition: all 0.4s ease;
        }
    </style>
</head>

<body class="antialiased text-slate-800">

    @include('layouts.navbar')

    <main class="max-w-7xl mx-auto px-6 pt-32 pb-20">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-12">
            <div>
                <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight">My Saved Services</h1>
                <p class="text-slate-500 mt-2 text-lg">Access and view your list of favourite services</p>
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
                    <div class="service-card group bg-white rounded-2xl border border-slate-200 hover:border-indigo-100 hover:shadow-xl transition-all duration-300 flex flex-col overflow-hidden relative">

                        {{-- Image Section --}}
                        <div class="relative h-56 bg-slate-200 overflow-hidden block">
                            <a href="{{ route('services.details', $service->id) }}">
                                <img src="{{ $service->image_path ? asset('storage/' . $service->image_path) : 'https://via.placeholder.com/800x600?text=No+Image' }}"
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>

                            {{-- Category Badge --}}
                            @if ($service->category)
                                <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold shadow-sm"
                                    style="color: {{ $service->category->color }}">
                                    {{ $service->category->name }}
                                </span>
                            @endif

                            {{-- REMOVE BUTTON (Integrated into new design) --}}
                            <button onclick="confirmRemove({{ $service->id }}, this)"
                                class="absolute top-4 right-4 bg-white/95 text-red-500 w-8 h-8 rounded-full flex items-center justify-center shadow-md hover:bg-red-500 hover:text-white transition-all transform active:scale-90"
                                title="Remove from favorites">
                                <i class="fa-solid fa-heart"></i>
                            </button>
                        </div>

                        {{-- Content Section --}}
                        <div class="p-5 flex flex-col flex-1">
                            {{-- User Info & Rating Row --}}
                            <div class="flex items-center gap-3 mb-3">
                                <img src="{{ $service->user->profile_photo_path ? asset('storage/' . $service->user->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($service->user->name) }}"
                                    class="w-8 h-8 rounded-full object-cover border border-slate-100">
                                
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-slate-900 flex items-center gap-1">
                                        {{ Str::limit($service->user->name, 15) }}
                                        @if ($service->user->trust_badge)
                                            <i class="fas fa-check-circle text-blue-500 text-[10px]"></i>
                                        @endif
                                    </span>
                                    <span class="text-[10px] text-slate-500">Student seller</span>
                                </div>

                                {{-- Rating Badge --}}
                                <div class="ml-auto flex items-center gap-1 bg-slate-50 px-2 py-1 rounded text-xs">
                                    <i class="fas fa-star text-yellow-400"></i>
                                    <span class="font-bold text-slate-700">
                                        {{ number_format($service->reviews_avg_rating ?? 0, 1) }}
                                    </span>
                                    <span class="text-slate-400">
                                        ({{ $service->reviews_count ?? 0 }})
                                    </span>
                                </div>
                            </div>

                            {{-- Title --}}
                            <a href="{{ route('services.details', $service->id) }}" class="block mb-2">
                                <h3 class="text-lg font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2 leading-tight">
                                    {{ $service->title }}
                                </h3>
                            </a>

                            {{-- Description --}}
                            <div class="text-sm text-slate-500 line-clamp-2 mb-4">
                                {{ Str::limit(strip_tags($service->description), 80) }}
                            </div>

                            {{-- Footer: Price & Button --}}
                            <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                <div>
                                    <span class="text-xs text-slate-400 font-medium uppercase">Starting at</span>
                                    <div class="text-lg font-bold text-slate-900">
                                        RM{{ number_format($service->basic_price, 0) }}
                                    </div>
                                </div>
                                <a href="{{ route('services.details', $service->id) }}"
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

    {{-- Javascript for Removal Logic --}}
    <script>
        function confirmRemove(serviceId, btn) {
            Swal.fire({
                title: 'Remove from favorites?',
                text: "You can always add this service back later.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444', // Red
                cancelButtonColor: '#64748b', // Slate
                confirmButtonText: 'Yes, remove it',
                cancelButtonText: 'Cancel',
                reverseButtons: true,
                borderRadius: '1rem',
                customClass: {
                    popup: 'rounded-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    performRemove(serviceId, btn);
                }
            });
        }

        function performRemove(serviceId, btn) {
            // Add loading state to button
            btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin"></i>';
            btn.disabled = true;

            fetch("{{ route('favorites.services.toggle') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        service_id: serviceId
                    })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        const card = btn.closest('.service-card');
                        card.classList.add('card-removed');

                        // Success Toast
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2000,
                            timerProgressBar: true
                        });
                        Toast.fire({
                            icon: 'success',
                            title: 'Service removed'
                        });

                        setTimeout(() => {
                            card.remove();
                            // Reload if list is empty to show empty state
                            if (document.querySelectorAll('.service-card').length === 0) {
                                location.reload();
                            }
                        }, 400);
                    }
                })
                .catch(err => {
                    Swal.fire('Error', 'Something went wrong!', 'error');
                    btn.innerHTML = '<i class="fa-solid fa-heart"></i>';
                    btn.disabled = false;
                });
        }
    </script>

</body>
</html>