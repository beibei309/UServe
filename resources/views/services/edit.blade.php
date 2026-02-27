@extends('layouts.helper')

{{-- 1. Initialize Schedule Data (Merge Defaults with Existing DB Data) --}}
@php
    $defaultSchedule = [
        'mon' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'tue' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'wed' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'thu' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'fri' => ['enabled' => true, 'start' => '09:00', 'end' => '17:00'],
        'sat' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
        'sun' => ['enabled' => false, 'start' => '10:00', 'end' => '14:00'],
    ];
    $scheduleData = $service->hss_operating_hours ?? $defaultSchedule;
    foreach ($defaultSchedule as $key => $val) {
        if (!isset($scheduleData[$key])) {
            $scheduleData[$key] = $val;
        }
    }

    $serviceImagePath = $service->hss_image_path ?? null;
    if ($serviceImagePath) {
        if (filter_var($serviceImagePath, FILTER_VALIDATE_URL)) {
            $serviceImageUrl = $serviceImagePath;
        } elseif (str_starts_with($serviceImagePath, 'storage/')) {
            $serviceImageUrl = asset($serviceImagePath);
        } else {
            $serviceImageUrl = asset('storage/' . ltrim($serviceImagePath, '/'));
        }
    } else {
        $serviceImageUrl = null;
    }
@endphp

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Libraries --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    {{-- Alpine JS (Only for Schedule Logic) --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Quill Customization */
        .ql-toolbar {
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
            border-color: #e5e7eb !important;
            background-color: #f9fafb;
        }

        .ql-container {
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
            border-color: #e5e7eb !important;
            font-family: inherit;
        }

        .ql-editor {
            min-height: 120px;
            font-size: 0.95rem;
        }

        /* Wizard Progress Line */
        .step-active {
            border-color: #4f46e5;
            color: #4f46e5;
        }

        .step-completed {
            border-color: #10b981;
            color: #10b981;
        }

        .step-inactive {
            border-color: transparent;
            color: #9ca3af;
        }

        /* Alpine Utilities */
        [x-cloak] {
            display: none !important;
        }

        /* Toggle Switch */
        .toggle-checkbox:checked {
            right: 0;
            border-color: #6366f1;
        }

        .toggle-checkbox:checked+.toggle-label {
            background-color: #6366f1;
        }

        .toggle-checkbox {
            right: 0;
            z-index: 1;
            border-color: #cbd5e1;
            transition: all 0.3s;
        }

        .toggle-label {
            width: 100%;
            height: 100%;
            background-color: #cbd5e1;
            border-radius: 9999px;
            transition: background-color 0.3s;
        }
    </style>

    <div class="py-12 bg-gray-50 min-h-screen">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Edit Service</h1>
                    <p class="text-gray-600 mt-1">Update details for <strong>{{ $service->hss_title }}</strong></p>
                </div>
                <a href="{{ route('services.manage') }}"
                    class="text-gray-600 hover:text-gray-900 font-medium flex items-center">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Back to Manage
                </a>
            </div>

            <div class="mb-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button
                            class="step-link step-active group w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors"
                            data-target="overview" onclick="switchTab('overview')">
                            <span
                                class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs mr-2 font-bold ring-1 ring-indigo-600">1</span>
                            Overview
                        </button>
                        <button
                            class="step-link step-inactive group w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors"
                            data-target="pricing" onclick="switchTab('pricing')">
                            <span
                                class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">2</span>
                            Pricing
                        </button>
                        <button
                            class="step-link step-inactive group w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors"
                            data-target="description" onclick="switchTab('description')">
                            <span
                                class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">3</span>
                            Description
                        </button>
                        <button
                            class="step-link step-inactive group w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors"
                            data-target="availability" onclick="switchTab('availability')">
                            <span
                                class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">4</span>
                            Availability
                        </button>
                    </nav>
                </div>
            </div>

            <form id="editServiceForm" method="POST" enctype="multipart/form-data"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative">
                @csrf
                @method('PUT') {{-- IMPORTANT: Method Spoofing for Edit --}}
                <input type="hidden" name="service_id" value="{{ $service->hss_id }}">

                <div id="overview" class="tab-section p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Service Basic</h2>
                        <p class="text-gray-500 text-sm">Core details about what you are offering.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 max-w-3xl">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Service Title <span
                                    class="text-red-500">*</span></label>
                            <input type="text" id="title" name="title" value="{{ $service->hss_title }}"
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span
                                    class="text-red-500">*</span></label>
                            <select id="category_id" name="category_id"
                                class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach ($categories as $category)
                                    <option value="{{ $category->hc_id }}"
                                        {{ $service->hss_category_id == $category->hc_id ? 'selected' : '' }}>{{ $category->hc_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Service Cover Image</label>

                            @if ($serviceImageUrl)
                                <div class="mb-4">
                                    <img src="{{ $serviceImageUrl }}" alt="Current Image"
                                        class="w-full h-48 object-cover rounded-lg mb-2 border border-gray-300">
                                </div>
                            @endif

                            <input type="file" id="image" name="image" accept="image/*"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">

                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                        <button type="button" onclick="nextStep('overview', 'pricing')"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                            Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="pricing" class="tab-section hidden p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Packages & Pricing</h2>
                        <p class="text-gray-500 text-sm">Define your session durations and costs.</p>
                    </div>

                    <div class="border border-gray-200 rounded-xl p-6 mb-6 bg-gray-50 relative">
                        <span
                            class="absolute top-0 right-0 px-3 py-1 bg-gray-200 text-gray-600 text-xs font-bold rounded-bl-xl rounded-tr-xl">REQUIRED</span>
                        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                            <span class="w-3 h-3 bg-gray-800 rounded-full mr-2"></span> Basic Package
                        </h3>
                        <input type="hidden" name="packages[0][package_type]" value="basic">

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Suggested Price From (RM) <span
                                        class="text-red-500">*</span></label>
                                <input type="number" id="basic_price" name="packages[0][price]"
                                    value="{{ $service->hss_basic_price }}"
                                    class="w-full mt-1 border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                            </div>
                             <div><label class="text-xs font-bold text-gray-600 uppercase">Duration</label><input
                                        type="text" name="packages[0][duration]" value="{{ $service->hss_basic_duration }}" placeholder="e.g. 2 Hours"
                                        class="w-full mt-1 border-gray-200 rounded-md"></div>
                            <div>
                                <label class="text-xs font-bold text-gray-500 uppercase">Frequency</label>
                                <input type="text" name="packages[0][frequency]" value="{{ $service->hss_basic_frequency }}" placeholder="e.g. per session"
                                    class="w-full mt-1 border-gray-300 rounded-md">
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">What's included?</label>
                            <div class="bg-white rounded-md border border-gray-300 overflow-hidden">
                                <div id="editor-basic" class="h-24">{!! $service->hss_basic_description !!}</div>
                            </div>
                            <input type="hidden" name="packages[0][description]" id="input-basic"
                                value="{{ $service->hss_basic_description }}">
                        </div>
                    </div>

                    <div class="flex items-center mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                        <input type="checkbox" id="offer_packages" name="offer_packages"
                            {{ $service->hss_standard_price || $service->hss_premium_price ? 'checked' : '' }}
                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                        <label for="offer_packages"
                            class="ml-3 block text-sm font-medium text-indigo-900 cursor-pointer select-none">
                            Offer <strong>Standard</strong> & <strong>Premium</strong> tiers (Upsell options)
                        </label>
                    </div>

                    <div id="extraPackages"
                        class="{{ $service->hss_standard_price || $service->hss_premium_price ? '' : 'hidden' }} space-y-6">
                        <div class="border border-blue-200 rounded-xl p-6 bg-blue-50/50">
                            <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center"><span
                                    class="w-3 h-3 bg-blue-600 rounded-full mr-2"></span> Standard Package</h3>
                            <input type="hidden" name="packages[1][package_type]" value="standard">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label class="text-xs font-bold text-blue-600 uppercase">Price</label><input
                                        type="number" name="packages[1][price]" value="{{ $service->hss_standard_price }}"
                                        class="w-full mt-1 border-blue-200 rounded-md"></div>
                                <div><label class="text-xs font-bold text-blue-600 uppercase">Duration</label><input
                                        type="text" name="packages[1][duration]" value="{{ $service->hss_standard_duration }}" placeholder="e.g. 2 Hours"
                                        class="w-full mt-1 border-blue-200 rounded-md"></div>
                                <div><label class="text-xs font-bold text-blue-600 uppercase">Frequency</label>
                                    <input type="text" name="packages[1][frequency]" value="{{ $service->hss_standard_frequency }}" placeholder="e.g. per session"
                                    class="w-full mt-1 border-blue-300 rounded-md">
                                </div>
                            </div>
                            <div class="bg-white rounded-md border border-blue-200 overflow-hidden">
                                <div id="editor-standard" class="h-20">{!! $service->hss_standard_description !!}</div>
                            </div>
                            <input type="hidden" name="packages[1][description]" id="input-standard"
                                value="{{ $service->hss_standard_description }}">
                        </div>

                        <div class="border border-purple-200 rounded-xl p-6 bg-purple-50/50">
                            <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center"><span
                                    class="w-3 h-3 bg-purple-600 rounded-full mr-2"></span> Premium Package</h3>
                            <input type="hidden" name="packages[2][package_type]" value="premium">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div><label class="text-xs font-bold text-purple-600 uppercase">Price</label><input
                                        type="number" name="packages[2][price]" value="{{ $service->hss_premium_price }}"
                                        class="w-full mt-1 border-purple-200 rounded-md"></div>
                                 <div><label class="text-xs font-bold text-purple-600 uppercase">Duration</label><input
                                        type="text" name="packages[2][duration]" value="{{ $service->hss_premium_duration }}" placeholder="e.g. 2 Hours"
                                        class="w-full mt-1 border-purple-200 rounded-md"></div>
                                <div><label class="text-xs font-bold text-purple-600 uppercase">Frequency</label>
                                     <input type="text" name="packages[2][frequency]" value="{{ $service->hss_premium_frequency }}" placeholder="e.g. per session"
                                    class="w-full mt-1 border-purple-300 rounded-md">
                                </div>
                            </div>
                            <div class="bg-white rounded-md border border-purple-200 overflow-hidden">
                                <div id="editor-premium" class="h-20">{!! $service->hss_premium_description !!}</div>
                            </div>
                            <input type="hidden" name="packages[2][description]" id="input-premium"
                                value="{{ $service->hss_premium_description }}">
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between">
                        <button type="button" onclick="nextStep('pricing', 'overview')"
                            class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium">
                            ← Back
                        </button>
                        <button type="button" onclick="nextStep('pricing', 'description')"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                            Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="description" class="tab-section hidden p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Detailed Description</h2>
                        <p class="text-gray-500 text-sm">Tell students why they should choose your service.</p>
                    </div>

                    <div class="mb-4">
                        <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
                            <div id="editor-main" class="h-64">{!! $service->hss_description !!}</div>
                        </div>
                        <input type="hidden" name="description" id="input-main" value="{{ $service->hss_description }}">
                        <p class="text-xs text-gray-400 mt-2 text-right">Be descriptive and professional.</p>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between">
                        <button type="button" onclick="nextStep('description', 'pricing')"
                            class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium">
                            ← Back
                        </button>
                        <button type="button" onclick="nextStep('description', 'availability')"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                            Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div id="availability" class="tab-section hidden p-8">
                    <div class="mb-6">
                        <h2 class="text-xl font-bold text-gray-900">Availability & Schedule</h2>
                        <p class="text-gray-500 text-sm">Update how and when students can book you.</p>
                    </div>

                    {{-- Alpine Component --}}
                    <div x-data="scheduleHandler()">

                        {{-- Booking Type Toggle --}}
                        <div
                            class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div>
                                <h3 class="font-bold text-indigo-900">How is this service booked?</h3>
                                <p class="text-sm text-indigo-700 mt-1">
                                    <span x-show="isSessionBased"><strong>Appointment Based:</strong> Users book specific
                                        time slots.</span>
                                    <span x-show="!isSessionBased"><strong>Task Based:</strong> One-off requests (no
                                        specific duration).</span>
                                </p>
                            </div>
                            <div class="flex items-center bg-white rounded-lg p-1 border border-indigo-200 shadow-sm">
                                <button type="button" @click="isSessionBased = true"
                                    :class="isSessionBased ? 'bg-indigo-600 text-white shadow-sm' :
                                        'text-gray-500 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-md text-sm font-bold transition-all">Time Slots</button>
                                <button type="button" @click="isSessionBased = false"
                                    :class="!isSessionBased ? 'bg-indigo-600 text-white shadow-sm' :
                                        'text-gray-500 hover:bg-gray-50'"
                                    class="px-4 py-2 rounded-md text-sm font-bold transition-all">One-off Task</button>
                            </div>
                            <input type="hidden" name="is_session_based" :value="isSessionBased ? 1 : 0">
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">

                            {{-- LEFT COLUMN --}}
                            <div class="lg:col-span-2 space-y-6">

                                {{-- Session Duration --}}
                                <div x-show="isSessionBased" x-transition
                                    class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                                    <div class="flex items-start gap-5">
                                        <div class="p-3.5 bg-indigo-50 rounded-2xl text-indigo-600"><i
                                                class="fa-regular fa-hourglass-half text-2xl"></i></div>
                                        <div class="flex-1">
                                            <h2 class="font-bold text-slate-800 text-lg">Session Duration</h2>
                                            <p class="text-sm text-slate-500 mb-6">How long is one slot?</p>
                                            <div class="w-full max-w-xs relative">
                                                <select name="session_duration" x-model.number="currentDuration"
                                                    @change="refreshPreview()" :disabled="!isSessionBased"
                                                    class="w-full rounded-xl border-slate-200 shadow-sm py-3 px-4 text-sm font-bold bg-slate-50">
                                                    @foreach ([15, 20, 30, 45, 60, 90, 120] as $opt)
                                                        <option value="{{ $opt }}"
                                                                {{ ($service->hss_session_duration ?? 60) == $opt ? 'selected' : '' }}>
                                                            {{ $opt }} Minutes</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Weekly Schedule --}}
                                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                    <div
                                        class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                        <h2 class="font-bold text-slate-800">Weekly Availability</h2>
                                        <button type="button" @click="showBulk = !showBulk"
                                            class="text-sm text-indigo-600 font-bold hover:text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg"><i
                                                class="fa-solid fa-sliders mr-1"></i> Bulk Edit</button>
                                    </div>
                                    {{-- Bulk Edit --}}
                                    <div x-show="showBulk"
                                        class="bg-indigo-50/80 p-4 border-b border-indigo-100 flex items-center gap-4">
                                        <span class="text-xs font-bold text-indigo-800 uppercase">Set all to:</span>
                                        <div
                                            class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-lg border border-indigo-100 shadow-sm">
                                            <input type="time" x-model="bulkStart"
                                                class="border-none p-0 text-xs font-bold text-slate-700">
                                            <span class="text-slate-300 text-xs">→</span>
                                            <input type="time" x-model="bulkEnd"
                                                class="border-none p-0 text-xs font-bold text-slate-700">
                                        </div>
                                        <button type="button" @click="applyBulkTime()"
                                            class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700 shadow-md">Apply</button>
                                    </div>
                                    {{-- Days Loop --}}
                                    <div class="divide-y divide-slate-100">
                                        <template x-for="day in days" :key="day.key">
                                            <div
                                                class="flex items-center justify-between p-5 hover:bg-slate-50 transition-colors group">
                                                <div class="flex items-center gap-5 w-48">
                                                    <div class="relative inline-block w-12 h-7">
                                                        <input type="checkbox"
                                                            :name="`operating_hours[${day.key}][enabled]`"
                                                            :id="`toggle-${day.key}`" x-model="schedule[day.key].enabled"
                                                            @change="refreshPreview()" value="1"
                                                            class="toggle-checkbox absolute block w-7 h-7 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-200 checked:right-0 checked:border-indigo-600 transition-all duration-300 shadow-sm" />
                                                        <label :for="`toggle-${day.key}`"
                                                            class="toggle-label block overflow-hidden h-7 rounded-full bg-slate-200 cursor-pointer"></label>
                                                    </div>
                                                    <label :for="`toggle-${day.key}`"
                                                        class="text-sm font-bold text-slate-700"
                                                        x-text="day.name"></label>
                                                </div>
                                                <div class="flex-1 flex justify-end">
                                                    <div x-show="schedule[day.key].enabled"
                                                        class="flex items-center gap-3">
                                                        <div
                                                            class="flex items-center bg-white border border-slate-200 rounded-lg px-3 py-1.5 shadow-sm">
                                                            <input type="time"
                                                                :name="`operating_hours[${day.key}][start]`"
                                                                x-model="schedule[day.key].start"
                                                                @change="refreshPreview()"
                                                                class="border-none p-0 text-sm font-bold text-slate-700">
                                                            <span class="text-slate-300 text-xs px-2 font-light">to</span>
                                                            <input type="time"
                                                                :name="`operating_hours[${day.key}][end]`"
                                                                x-model="schedule[day.key].end" @change="refreshPreview()"
                                                                class="border-none p-0 text-sm font-bold text-slate-700">
                                                        </div>
                                                    </div>
                                                    <div x-show="!schedule[day.key].enabled"
                                                        class="text-xs font-bold text-slate-400 py-2 px-5 bg-slate-100 rounded-lg uppercase tracking-wider">
                                                        Closed</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                {{-- 🟢 NEW: PREVIEW SECTION (Student View Simulator) --}}
                                {{-- 🟢 NEW: PREVIEW & BLOCKING SECTION --}}
                                <div x-show="isSessionBased"
                                    class="bg-slate-50 rounded-3xl border border-slate-200 p-6 relative">
                                    <div class="flex flex-col md:flex-row justify-between items-center mb-4 gap-4">
                                        <div>
                                            <h3 class="font-bold text-slate-800"><i
                                                    class="fa-regular fa-eye mr-2 text-indigo-500"></i>Manage Slots</h3>
                                            <p class="text-xs text-slate-500">Click a green slot to <span
                                                    class="text-red-500 font-bold">block</span> it.</p>
                                        </div>
                                        <input type="text" id="preview-date-picker"
                                            class="text-xs border-slate-200 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500 w-full md:w-auto"
                                            placeholder="Pick date">
                                    </div>

                                    {{-- Hidden Input to send Blocked Slots to Backend --}}
                                    <input type="hidden" name="blocked_slots" :value="JSON.stringify(blockedSlots)">

                                    {{-- Empty State --}}
                                    <div x-show="previewSlots.length === 0"
                                        class="text-center py-8 text-slate-400 text-sm border-2 border-dashed border-slate-200 rounded-xl">
                                        <span x-text="previewMessage"></span>
                                    </div>

                                    {{-- THE GRID --}}
                                    <div class="grid grid-cols-2 gap-3" x-show="previewSlots.length > 0">
                                        <template x-for="slot in previewSlots" :key="slot.start">

                                            <button type="button" @click="toggleSlotBlock(slot)"
                                                :disabled="slot.isBooked"
                                                class="relative py-3 px-2 border rounded-xl flex flex-col items-center justify-center text-center h-20 transition-all duration-200"
                                                :class="{
                                                    'bg-red-50 border-red-100 opacity-60 cursor-not-allowed': slot
                                                        .isBooked,
                                                    'bg-gray-100 border-gray-300 hover:bg-gray-200': slot.isBlocked && !
                                                        slot.isBooked,
                                                    'bg-white border-green-200 shadow-sm hover:border-green-400 hover:shadow-md':
                                                        !slot.isBooked && !slot.isBlocked
                                                }">

                                                {{-- Time Display --}}
                                                <span class="text-xs font-bold tracking-wide"
                                                    :class="{
                                                        'line-through text-slate-400': slot
                                                            .isBooked,
                                                        'text-gray-500': slot.isBlocked,
                                                        'text-slate-700': !
                                                            slot.isBooked && !slot.isBlocked
                                                    }"
                                                    x-text="slot.display"></span>

                                                {{-- Status Label --}}
                                                <div class="mt-1">
                                                    {{-- 1. Real Booking from DB --}}
                                                    <template x-if="slot.isBooked">
                                                        <span
                                                            class="text-[10px] font-extrabold text-red-400 uppercase tracking-widest bg-red-100 px-2 py-0.5 rounded">Booked</span>
                                                    </template>

                                                    {{-- 2. Manually Blocked by User --}}
                                                    <template x-if="slot.isBlocked && !slot.isBooked">
                                                        <span
                                                            class="text-[10px] font-bold text-red-500 uppercase tracking-wide flex items-center gap-1">
                                                            <i class="fa-solid fa-ban text-[9px]"></i> Booked
                                                        </span>
                                                    </template>

                                                    {{-- 3. Available --}}
                                                    <template x-if="!slot.isBooked && !slot.isBlocked">
                                                        <span
                                                            class="text-[10px] text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded border border-green-100">Available</span>
                                                    </template>
                                                </div>

                                            </button>

                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- RIGHT COLUMN (Block Dates) --}}
                            {{-- RIGHT COLUMN (Block Dates) --}}
                            <div class="lg:col-span-1 space-y-6">
                                {{-- Kita tambah x-data di sini untuk kawal UI bila toggle ditekan --}}
                                {{-- Pastikan tukar $service->is_active mengikut nama column DB anda (contoh: is_unavailable atau active status) --}}
                                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sticky top-24"
                                    x-data="{ isUnavailable: {{ $service->hss_is_active ? 'false' : 'true' }} }">

                                    {{-- 1. TOGGLE UNAVAILABLE (BARU) --}}
                                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-slate-100">
                                        <div>
                                            <h2 class="font-bold text-slate-800">Temporary Unavailable</h2>
                                            <p class="text-xs text-slate-500">Close bookings indefinitely.</p>
                                        </div>

                                        <div class="relative inline-block w-12 h-7">
                                            {{-- Input ini hantar '1' jika unavailable. Controller perlu handle logic ini. --}}
                                            <input type="checkbox" name="is_unavailable" id="toggle-unavailable"
                                                class="toggle-checkbox absolute block w-7 h-7 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-200 checked:right-0 checked:border-indigo-600 transition-all duration-300 shadow-sm"
                                                x-model="isUnavailable" value="1" {{-- Jika DB guna is_active, logic ini mungkin terbalik --}}
                                                {{ !$service->hss_is_active ? 'checked' : '' }} />
                                            <label for="toggle-unavailable"
                                                class="toggle-label block overflow-hidden h-7 rounded-full bg-slate-200 cursor-pointer"></label>
                                        </div>
                                    </div>

                                    {{-- UI Selection Dates (Akan kabur jika toggle unavailable ON) --}}
                                    <div class="transition-opacity duration-300"
                                        :class="isUnavailable ? 'opacity-40 pointer-events-none select-none' : ''">
                                        <h2 class="font-bold text-slate-800 mb-2">Block Specific Dates</h2>
                                        <p class="text-xs text-slate-500 mb-6">Select specific dates (like holidays) when
                                            you are unavailable.</p>

                                        <div class="relative mb-4">
                                            <input id="unavailableDates" name="unavailable_dates"
                                                class="w-full pl-10 px-4 py-3 border border-slate-200 rounded-xl bg-white focus:ring-indigo-500"
                                                placeholder="Select dates..."
                                                value="{{ $service->hss_unavailable_dates ? implode(',', json_decode($service->hss_unavailable_dates, true)) : '' }}">
                                            <i class="fa-regular fa-calendar absolute left-3.5 top-3.5 text-slate-400"></i>
                                        </div>

                                        {{-- 2. BUTANG TAMBAHAN (2 Weeks / 2 Months) --}}
                                        <div class="grid grid-cols-2 gap-3 mb-3">
                                            <button type="button" onclick="quickBlockDates(1, 'week')"
                                                class="text-xs bg-slate-50 border border-slate-200 text-slate-600 py-2 rounded-lg hover:bg-slate-100 font-medium">
                                                + 1 Week
                                            </button>    
                                            <button type="button" onclick="quickBlockDates(1, 'month')"
                                                class="text-xs bg-slate-50 border border-slate-200 text-slate-600 py-2 rounded-lg hover:bg-slate-100 font-medium">
                                                + 1 Month
                                            </button>
                                        </div>

                                        <button type="button" onclick="clearUnavailableDates()"
                                            class="w-full text-xs bg-red-50 text-red-600 border border-red-100 py-2 rounded-lg hover:bg-red-100 font-bold transition-colors">
                                            <i class="fa-solid fa-trash-can mr-1"></i> Clear All Dates
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Save Buttons --}}
                    <div class="bg-gray-50 rounded-xl p-8 text-center border border-gray-100 mt-8">
                        <h3 class="text-lg font-bold text-gray-900">Update Service?</h3>
                        <div class="flex justify-center gap-4 mt-4">
                            <button type="button" onclick="nextStep('availability', 'description')"
                                class="px-5 py-3 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition">Review
                                Details</button>
                            <button type="button" onclick="submitForm()"
                                class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 transition shadow-lg flex items-center">Save
                                Changes</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        // --- 1. QUILL CONFIG ---
        const toolbarOptions = [
            ['bold', 'italic', 'underline'],
            [{
                'list': 'bullet'
            }]
        ];

        function setupQuill(editorId, inputId, placeholder) {
            if (document.getElementById(editorId)) {
                var quill = new Quill('#' + editorId, {
                    theme: 'snow',
                    modules: {
                        toolbar: toolbarOptions
                    },
                    placeholder: placeholder
                });
                quill.on('text-change', function() {
                    document.getElementById(inputId).value = quill.root.innerHTML;
                });
            }
        }
        document.addEventListener('DOMContentLoaded', function() {
            setupQuill('editor-basic', 'input-basic', 'e.g. 1 hour online consultation...');
            setupQuill('editor-standard', 'input-standard', 'Describe standard package...');
            setupQuill('editor-premium', 'input-premium', 'Describe premium package...');
            setupQuill('editor-main', 'input-main', 'Provide a comprehensive description of your service...');
        });

        // --- 2. TEMPLATE IMAGE ---
        document.querySelectorAll('.template-image').forEach(img => {
            img.addEventListener('click', function() {
                document.querySelectorAll('.template-image').forEach(i => i.classList.remove('ring-4',
                    'ring-indigo-300'));
                this.classList.add('ring-4', 'ring-indigo-300');
                document.getElementById('template_image').value = this.dataset.val;
                document.getElementById('image').value = "";
            });
        });

        // --- 3. EXTRA PACKAGES ---
        document.getElementById('offer_packages').addEventListener('change', function() {
            const extra = document.getElementById('extraPackages');
            if (this.checked) extra.classList.remove('hidden');
            else extra.classList.add('hidden');
        });

        // --- 4. ALPINE SCHEDULE ---
        function scheduleHandler() {
            return {
                isSessionBased: @json($service->hss_session_duration !== null),
                currentDuration: @json($service->hss_session_duration ?? 60),

                // Data from Controller
                schedule: @json($scheduleData),
                bookedSlots: @json($bookedSlots ?? []), // Real bookings (cannot change)

                // 🟢 NEW: Manually Blocked Slots (e.g., ["2025-12-17 14:00"])
                blockedSlots: @json($service->blocked_slots ?? []),

                days: [{
                        key: 'mon',
                        name: 'Monday'
                    }, {
                        key: 'tue',
                        name: 'Tuesday'
                    },
                    {
                        key: 'wed',
                        name: 'Wednesday'
                    }, {
                        key: 'thu',
                        name: 'Thursday'
                    },
                    {
                        key: 'fri',
                        name: 'Friday'
                    }, {
                        key: 'sat',
                        name: 'Saturday'
                    },
                    {
                        key: 'sun',
                        name: 'Sunday'
                    }
                ],

                showBulk: false,
                bulkStart: '09:00',
                bulkEnd: '17:00',

                // Preview Data
                previewDate: null,
                previewSlots: [],
                previewMessage: 'Select a date above to manage slots.',

                init() {
                    flatpickr("#preview-date-picker", {
                        minDate: "today",
                        defaultDate: new Date(), // Select today by default
                        onChange: (selectedDates, dateStr) => {
                            this.previewDate = dateStr;
                            this.generatePreview();
                        }
                    });
                    // Initial Load
                    this.previewDate = new Date().toISOString().split('T')[0];
                    this.$nextTick(() => {
                        this.generatePreview();
                    });
                },

                // 🟢 NEW FUNCTION: Toggle Block Status
                toggleSlotBlock(slot) {
                    // Cannot toggle real bookings
                    if (slot.isBooked) return;

                    // Construct the key (e.g., "2025-12-17 14:00")
                    const slotKey = `${this.previewDate} ${slot.start}`;

                    if (this.blockedSlots.includes(slotKey)) {
                        // If exists, remove it (Unblock)
                        this.blockedSlots = this.blockedSlots.filter(s => s !== slotKey);
                    } else {
                        // If not exists, add it (Block)
                        this.blockedSlots.push(slotKey);
                    }

                    // Regenerate view to reflect changes
                    this.generatePreview();
                },

                generatePreview() {
                    if (!this.previewDate) return;
                    this.previewSlots = [];

                    // 1. Get Day Settings
                    const d = new Date(this.previewDate);
                    const dayName = d.toLocaleDateString('en-US', {
                        weekday: 'short'
                    }).toLowerCase();
                    const daySettings = this.schedule[dayName];

                    if (!daySettings || !daySettings.enabled) {
                        this.previewMessage = 'Service is closed on ' + dayName + 's.';
                        return;
                    }

                    // 2. Loop Time
                    let startMinutes = this.timeToMinutes(daySettings.start);
                    let endMinutes = this.timeToMinutes(daySettings.end);
                    let duration = parseInt(this.currentDuration);

                    for (let time = startMinutes; time + duration <= endMinutes; time += duration) {
                        let startTimeStr = this.minutesToTime(time);
                        let endTimeStr = this.minutesToTime(time + duration);

                        // Key identifier
                        let slotKey = `${this.previewDate} ${startTimeStr}`;

                        // Check Statuses
                        let isBooked = this.bookedSlots.includes(slotKey); // Database booking
                        let isBlocked = this.blockedSlots.includes(slotKey); // Manual Block

                        this.previewSlots.push({
                            start: startTimeStr,
                            display: `${this.formatAmPm(startTimeStr)} - ${this.formatAmPm(endTimeStr)}`,
                            isBooked: isBooked,
                            isBlocked: isBlocked // Pass block status to view
                        });
                    }

                    if (this.previewSlots.length === 0) this.previewMessage = 'No slots available settings.';
                },

                // --- Helpers ---
                applyBulkTime() {
                    for (const dayKey in this.schedule) {
                        if (this.schedule[dayKey].enabled) {
                            this.schedule[dayKey].start = this.bulkStart;
                            this.schedule[dayKey].end = this.bulkEnd;
                        }
                    }
                    this.showBulk = false;
                    this.generatePreview();
                },
                timeToMinutes(time) {
                    const [h, m] = time.split(':').map(Number);
                    return h * 60 + m;
                },
                minutesToTime(minutes) {
                    const h = Math.floor(minutes / 60);
                    const m = minutes % 60;
                    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
                },
                formatAmPm(time) {
                    let [h, m] = time.split(':');
                    h = parseInt(h);
                    let ampm = h >= 12 ? 'PM' : 'AM';
                    h = h % 12;
                    h = h ? h : 12;
                    return `${h}:${m} ${ampm}`;
                }
            }
        }

        // --- 5. FLATPICKR ---
        let fpInstance;
        document.addEventListener('DOMContentLoaded', function() {
            fpInstance = flatpickr("#unavailableDates", {
                mode: "multiple",
                dateFormat: "Y-m-d",
                minDate: "today",
                conjunction: ", ",
                defaultDate: @json($service->hss_unavailable_dates ?? []),
                locale: {
                    firstDayOfWeek: 1
                }
            });
        });

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }
        window.quickBlockDates = function(amount, unit) {
            if (!fpInstance) return;
            let daysToAdd = unit === 'week' ? amount * 7 : amount * 30,
                newDates = [],
                today = new Date();
            for (let i = 0; i < daysToAdd; i++) {
                let d = new Date(today);
                d.setDate(today.getDate() + i);
                newDates.push(formatDate(d));
            }
            let current = fpInstance.selectedDates.map(d => formatDate(d));
            fpInstance.setDate([...new Set([...current, ...newDates])], true);
        };
        window.clearUnavailableDates = function() {
            if (fpInstance) fpInstance.clear();
        };

        // --- 6. NAVIGATION LOGIC (VANILLA JS TO MATCH CREATE) ---
        function switchTab(targetId) {
            document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
            document.getElementById(targetId).classList.remove('hidden');
            updateHeader(targetId);
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        function nextStep(currentId, nextId) {
            // Validate Basic
            if (currentId === 'overview' && nextId === 'pricing') {
                if (!document.getElementById('title').value || !document.getElementById('category_id').value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required Fields',
                        text: 'Please provide a Title and Category.'
                    });
                    return;
                }
            }
            // Validate Price
            if (currentId === 'pricing' && nextId === 'description') {
                if (!document.getElementById('basic_price').value) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required Fields',
                        text: 'Please set a price for the Basic Package.'
                    });
                    return;
                }
            }
            switchTab(nextId);
        }

        function updateHeader(activeId) {
            const map = {
                'overview': 0,
                'pricing': 1,
                'description': 2,
                'availability': 3
            };
            const activeIndex = map[activeId];
            const links = document.querySelectorAll('.step-link');
            links.forEach((link, index) => {
                link.className =
                    "step-link w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors"; // Base
                const circle = link.querySelector('span');
                if (index < activeIndex) {
                    link.classList.add('step-completed', 'border-green-500', 'text-green-600');
                    circle.className =
                        "w-6 h-6 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs mr-2 font-bold";
                    circle.innerHTML = "✓";
                } else if (index === activeIndex) {
                    link.classList.add('step-active', 'border-indigo-500', 'text-indigo-600');
                    circle.className =
                        "w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs mr-2 font-bold ring-1 ring-indigo-600";
                    circle.innerHTML = index + 1;
                } else {
                    link.classList.add('step-inactive', 'border-transparent', 'text-gray-400');
                    circle.className =
                        "w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold";
                    circle.innerHTML = index + 1;
                }
            });
        }

        // --- 7. FINAL SUBMISSION ---
       // --- 7. FINAL SUBMISSION (Debug Version) ---
async function submitForm() {
    const form = document.getElementById('editServiceForm');
    
    // Sync Flatpickr to hidden input
    if (fpInstance) document.getElementById('unavailableDates').value = fpInstance.input.value;

    const formData = new FormData(form);

    Swal.fire({
        title: 'Saving Changes',
        text: 'Updating...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });

    try {
        const response = await fetch("{{ route('services.update', $service->hss_id) }}", {
            method: 'POST', // POST is safer for file uploads
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json' 
            },
            body: formData
        });

        // Check if response is JSON
        const contentType = response.headers.get("content-type");
        if (contentType && contentType.indexOf("application/json") !== -1) {
            const data = await response.json();
            
            if (!response.ok) {
                // Validation Error (422)
                let errorMsg = data.message || 'Validation failed';
                if(data.errors) {
                    // Show the first validation error found
                    errorMsg = Object.values(data.errors)[0][0];
                }
                throw new Error(errorMsg);
            }

            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Updated Successfully!',
                    confirmButtonColor: '#10b981'
                }).then(() => window.location.href = "{{ route('services.manage') }}");
            } else {
                throw new Error(data.error || 'Unknown error');
            }
        } else {
            // Server returned HTML (500 Error Page)
            const text = await response.text();
            console.error("Server Error HTML:", text); // Check Console for full details
            throw new Error("Server Error (500). Check console for details.");
        }

    } catch (error) {
        console.error(error);
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: error.message
        });
    }
}    </script>
@endsection
