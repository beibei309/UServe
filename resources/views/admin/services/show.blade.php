@extends('admin.layout')

@section('content')

<div class="px-6 py-4">

    {{-- HEADER --}}
    <div class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold text-gray-800">
            Service Details
        </h1>

        <a href="{{ route('admin.services.index') }}"
           class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
            Back
        </a>
    </div>

    {{-- CARD --}}
    <div class="bg-white shadow rounded-lg p-5">

        {{-- TOP LAYOUT --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- IMAGE --}}
            <div class="rounded-lg overflow-hidden border bg-gray-100 h-64">
                @if ($service->image_path)
                    @php
                        $path = $service->image_path;
                        // 1. Check if external URL
                        if (Str::startsWith($path, ['http://', 'https://'])) {
                            $imageUrl = $path;
                        } 
                        // 2. Check if file exists in 'storage' (public/storage/...)
                        elseif (file_exists(public_path('storage/' . $path))) {
                            $imageUrl = asset('storage/' . $path);
                        } 
                        // 3. Fallback: Assume it's in public root (public/...)
                        else {
                            $imageUrl = asset($path);
                        }
                    @endphp
                    <img src="{{ $imageUrl }}" 
                         alt="{{ $service->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="flex items-center justify-center h-full text-gray-400">
                        No Image
                    </div>
                @endif
            </div>

            {{-- DETAILS --}}
            <div>
                <h2 class="text-xl font-bold text-gray-900 mb-1">
                    {{ $service->title }}
                </h2>

                {{-- Category --}}
                <div class="mb-2">
                    @if($service->category)
                        <span class="px-3 py-1 text-xs text-white rounded-full"
                              style="background: {{ $service->category->color }}">
                            {{ $service->category->name }}
                        </span>
                    @endif
                </div>

                {{-- Status --}}
                <div class="mb-3">
                    @if ($service->approval_status === 'approved')
                        <span class="badge-green">Approved</span>
                    @elseif($service->approval_status === 'rejected')
                        <span class="badge-red">Rejected</span>
                    @elseif($service->approval_status === 'suspended')
                        <span class="badge-dark">Suspended</span>
                    @else
                        <span class="badge-yellow">Pending</span>
                    @endif
                </div>

                {{-- Provider --}}
                <p class="text-sm text-gray-600">
                    <strong>Provider:</strong> {{ $service->user->name ?? 'Unknown' }}
                </p>

                {{-- Rating --}}
                <p class="text-sm text-gray-600 mt-1">
                    <strong>Rating:</strong> 
                    {{ number_format($service->reviews_avg_rating ?? 0,1) }} ⭐
                    ({{ $service->reviews_count }} reviews)
                </p>

                {{-- Created --}}
                <p class="text-xs text-gray-500 mt-1">
                    Created at: {{ $service->created_at->format('d M Y, h:i A') }}
                </p>
            </div>

        </div>

        {{-- DESCRIPTION --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-800 mb-2">Description</h3>
            <div class="bg-gray-50 border rounded p-3 text-sm text-gray-700">
                {!! $service->description !!}
            </div>
        </div>

        {{-- PRICING --}}
        <div class="mt-6">
            <h3 class="font-bold text-gray-800 mb-2">Packages</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- BASIC --}}
                @if($service->basic_price)
                <div class="p-4 bg-blue-50 border rounded">
                    <p class="text-xs font-bold text-blue-700 uppercase">Basic</p>
                    <p class="text-lg font-bold">RM {{ $service->basic_price }}</p>
                    <p class="text-xs text-gray-600">
                        {{ $service->basic_description ?? 'No description' }}
                    </p>
                </div>
                @endif

                {{-- STANDARD --}}
                @if($service->standard_price)
                <div class="p-4 bg-yellow-50 border rounded">
                    <p class="text-xs font-bold text-yellow-700 uppercase">Standard</p>
                    <p class="text-lg font-bold">RM {{ $service->standard_price }}</p>
                    <p class="text-xs text-gray-600">
                        {{ $service->standard_description ?? 'No description' }}
                    </p>
                </div>
                @endif

                {{-- PREMIUM --}}
                @if($service->premium_price)
                <div class="p-4 bg-purple-50 border rounded">
                    <p class="text-xs font-bold text-purple-700 uppercase">Premium</p>
                    <p class="text-lg font-bold">RM {{ $service->premium_price }}</p>
                    <p class="text-xs text-gray-600">
                        {{ $service->premium_description ?? 'No description' }}
                    </p>
                </div>
                @endif

            </div>
        </div>

    </div>

</div>

@endsection
