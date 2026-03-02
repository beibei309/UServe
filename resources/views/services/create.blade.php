@extends('layouts.helper')

{{-- 1. Initialize Default Schedule (PHP) --}}
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
@endphp

@section('content')
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- Libraries --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

<style>
    /* Quill Customization */
    .ql-toolbar { border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem; border-color: #e5e7eb !important; background-color: #f9fafb; }
    .ql-container { border-bottom-left-radius: 0.5rem; border-bottom-right-radius: 0.5rem; border-color: #e5e7eb !important; font-family: inherit; }
    .ql-editor { min-height: 120px; font-size: 0.95rem; }
    
    /* Wizard Steps */
    .step-active { border-color: #4f46e5; color: #4f46e5; }
    .step-completed { border-color: #10b981; color: #10b981; }
    .step-inactive { border-color: transparent; color: #9ca3af; }
    
    [x-cloak] { display: none !important; }

    /* Custom Toggle */
    .toggle-checkbox:checked { right: 0; border-color: #6366f1; }
    .toggle-checkbox:checked + .toggle-label { background-color: #6366f1; }
    .toggle-checkbox { right: 0; z-index: 1; border-color: #cbd5e1; transition: all 0.3s; }
    .toggle-label { width: 100%; height: 100%; background-color: #cbd5e1; border-radius: 9999px; transition: background-color 0.3s; }
</style>

<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Create New Service</h1>
                <p class="text-gray-600 mt-1">Fill in the details below to list your service.</p>
            </div>
            <a href="{{ route('services.manage') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Back to Manage
            </a>
        </div>

        {{-- Wizard Tabs --}}
        <div class="mb-8 max-w-5xl mx-auto">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button class="step-link step-active w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors" data-target="overview">
                        <span class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center text-xs mr-2 font-bold ring-1 ring-indigo-600">1</span> Overview
                    </button>
                    <button class="step-link step-inactive w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors pointer-events-none" data-target="pricing">
                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">2</span> Pricing
                    </button>
                    <button class="step-link step-inactive w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors pointer-events-none" data-target="description">
                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">3</span> Description
                    </button>
                    <button class="step-link step-inactive w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors pointer-events-none" data-target="availability">
                        <span class="w-6 h-6 rounded-full bg-gray-100 text-gray-500 flex items-center justify-center text-xs mr-2 font-bold">4</span> Availability
                    </button>
                </nav>
            </div>
        </div>

        {{-- Main Form --}}
        <form id="createServiceForm" method="POST" enctype="multipart/form-data" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden relative max-w-5xl mx-auto">
            @csrf
            
            {{-- STEP 1: OVERVIEW --}}
            <div id="overview" class="tab-section p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Service Basics</h2>
                    <p class="text-gray-500 text-sm">Let's start with the fundamental details.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 max-w-3xl">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Service Title <span class="text-red-500">*</span></label>
                        <input type="text" id="title" name="title" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" placeholder="e.g. Professional Mathematics Tutoring">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Category <span class="text-red-500">*</span></label>
                        <select id="category_id" name="category_id" class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                            <option value="">Select a category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->hc_id }}">{{ $category->hc_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Service Cover Image</label>
                        <input type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end">
                    <button type="button" onclick="nextStep('overview', 'pricing')" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                        Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            {{-- STEP 2: PRICING --}}
            <div id="pricing" class="tab-section hidden p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Packages & Pricing</h2>
                    <p class="text-gray-500 text-sm">Define your costs and what students get per package.</p>
                    <p class="text-gray-400 text-xs mt-1">Duration = how long the service takes (e.g. 1 hour). Billing Unit = how price is charged (e.g. per session / per hour).</p>
                </div>

                {{-- Basic Package (Required) --}}
                <div class="border border-gray-200 rounded-xl p-6 mb-6 bg-gray-50 relative">
                    <span class="absolute top-0 right-0 px-3 py-1 bg-gray-200 text-gray-600 text-xs font-bold rounded-bl-xl rounded-tr-xl">REQUIRED</span>
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center"><span class="w-3 h-3 bg-gray-800 rounded-full mr-2"></span> Basic Package</h3>
                    <input type="hidden" name="packages[0][package_type]" value="basic">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Price (RM) <span class="text-red-500">*</span></label>
                            <input type="number" id="basic_price" name="packages[0][price]" class="w-full mt-1 border-gray-300 rounded-md focus:ring-gray-500 focus:border-gray-500">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Duration (shown to student)</label>
                            <input type="text" name="packages[0][duration]" placeholder="e.g. 1 hour" class="w-full mt-1 border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label class="text-xs font-bold text-gray-500 uppercase">Billing Unit</label>
                            <input type="text" name="packages[0][frequency]" placeholder="e.g. per session" class="w-full mt-1 border-gray-300 rounded-md">
                            <p class="mt-1 text-[11px] text-gray-400">Examples: per session, per hour, per day, per task, per page</p>
                        </div>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">What's included?</label>
                        <div class="bg-white rounded-md border border-gray-300 overflow-hidden">
                            <div id="editor-basic" class="h-24"></div>
                        </div>
                        <input type="hidden" name="packages[0][description]" id="input-basic">
                    </div>
                </div>

                {{-- Tiers Toggle --}}
                <div class="flex items-center mb-6 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                    <input type="checkbox" id="offer_packages" name="offer_packages" class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded cursor-pointer">
                    <label for="offer_packages" class="ml-3 block text-sm font-medium text-indigo-900 cursor-pointer select-none">
                        Offer <strong>Standard</strong> & <strong>Premium</strong> tiers
                    </label>
                </div>

                {{-- Extra Packages --}}
                <div id="extraPackages" class="hidden space-y-6">
                    {{-- Standard --}}
                    <div class="border border-blue-200 rounded-xl p-6 bg-blue-50/50">
                        <h3 class="text-lg font-bold text-blue-800 mb-4 flex items-center"><span class="w-3 h-3 bg-blue-600 rounded-full mr-2"></span> Standard Package</h3>
                        <input type="hidden" name="packages[1][package_type]" value="standard">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div><label class="text-xs font-bold text-blue-600 uppercase">Price (RM)</label><input type="number" name="packages[1][price]" class="w-full mt-1 border-blue-200 rounded-md"></div>
                            <div><label class="text-xs font-bold text-blue-600 uppercase">Duration (shown to student)</label><input type="text" name="packages[1][duration]" placeholder="e.g. 2 hours" class="w-full mt-1 border-blue-200 rounded-md"></div>
                            <div><label class="text-xs font-bold text-blue-600 uppercase">Billing Unit</label><input type="text" name="packages[1][frequency]" placeholder="e.g. per session" class="w-full mt-1 border-blue-300 rounded-md"><p class="mt-1 text-[11px] text-blue-400">Examples: per session, per hour, per day, per task, per page</p></div>
                        </div>
                        <div class="bg-white rounded-md border border-blue-200 overflow-hidden"><div id="editor-standard" class="h-20"></div></div>
                        <input type="hidden" name="packages[1][description]" id="input-standard">
                    </div>
                    {{-- Premium --}}
                    <div class="border border-purple-200 rounded-xl p-6 bg-purple-50/50">
                        <h3 class="text-lg font-bold text-purple-800 mb-4 flex items-center"><span class="w-3 h-3 bg-purple-600 rounded-full mr-2"></span> Premium Package</h3>
                        <input type="hidden" name="packages[2][package_type]" value="premium">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div><label class="text-xs font-bold text-purple-600 uppercase">Price (RM)</label><input type="number" name="packages[2][price]" class="w-full mt-1 border-purple-200 rounded-md"></div>
                            <div><label class="text-xs font-bold text-purple-600 uppercase">Duration (shown to student)</label><input type="text" name="packages[2][duration]" placeholder="e.g. 3 hours" class="w-full mt-1 border-purple-200 rounded-md"></div>
                            <div><label class="text-xs font-bold text-purple-600 uppercase">Billing Unit</label><input type="text" name="packages[2][frequency]" placeholder="e.g. per session" class="w-full mt-1 border-purple-300 rounded-md"><p class="mt-1 text-[11px] text-purple-400">Examples: per session, per hour, per day, per task, per page</p></div>
                        </div>
                        <div class="bg-white rounded-md border border-purple-200 overflow-hidden"><div id="editor-premium" class="h-20"></div></div>
                        <input type="hidden" name="packages[2][description]" id="input-premium">
                    </div>
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between">
                    <button type="button" onclick="nextStep('pricing', 'overview')" class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium">Back</button>
                    <button type="button" onclick="nextStep('pricing', 'description')" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                        Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            {{-- STEP 3: DESCRIPTION --}}
            <div id="description" class="tab-section hidden p-8">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-900">Detailed Description</h2>
                    <p class="text-gray-500 text-sm">Tell students why they should choose your service.</p>
                </div>
                <div class="mb-4">
                    <div class="bg-white rounded-lg border border-gray-300 overflow-hidden">
                        <div id="editor-main" class="h-64"></div>
                    </div>
                    <input type="hidden" name="description" id="input-main">
                    <p class="text-xs text-gray-400 mt-2 text-right">Be descriptive and professional.</p>
                </div>
                <div class="mt-8 pt-6 border-t border-gray-100 flex justify-between">
                    <button type="button" onclick="nextStep('description', 'pricing')" class="px-5 py-2.5 text-gray-600 hover:text-gray-900 font-medium"> ← Back</button>
                    <button type="button" onclick="nextStep('description', 'availability')" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition shadow-sm flex items-center">
                        Next Step <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                    </button>
                </div>
            </div>

            {{-- STEP 4: AVAILABILITY --}}
            <div id="availability" class="tab-section hidden p-8">
                {{-- Alpine Component --}}
                <div x-data="scheduleHandler()">
                    
                    {{-- Booking Type Toggle --}}
                    <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-5 mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h3 class="font-bold text-indigo-900">How is this service booked?</h3>
                            <p class="text-sm text-indigo-700 mt-1">
                                <span x-show="isSessionBased"><strong>Appointment Based:</strong> Users book specific time slots.</span>
                                <span x-show="!isSessionBased"><strong>Task Based:</strong> One-off requests (e.g. Laundry).</span>
                            </p>
                        </div>
                        <div class="flex items-center bg-white rounded-lg p-1 border border-indigo-200 shadow-sm">
                            <button type="button" @click="isSessionBased = true" :class="isSessionBased ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-md text-sm font-bold transition-all">Time Slots</button>
                            <button type="button" @click="isSessionBased = false" :class="!isSessionBased ? 'bg-indigo-600 text-white shadow-sm' : 'text-gray-500 hover:bg-gray-50'" class="px-4 py-2 rounded-md text-sm font-bold transition-all">One-off Task</button>
                        </div>
                        <input type="hidden" name="is_session_based" :value="isSessionBased ? 1 : 0">
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
                        <div class="lg:col-span-2 space-y-6">
                            {{-- Session Duration --}}
                            <div x-show="isSessionBased" x-transition class="bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
                                <h2 class="font-bold text-slate-800 text-lg">Session Duration</h2>
                                <p class="text-sm text-slate-500 mb-6">How long is one slot?</p>
                                <select name="session_duration" :disabled="!isSessionBased" class="w-full max-w-xs rounded-xl border-slate-200 shadow-sm focus:border-indigo-500 py-3 px-4 font-bold text-slate-700 bg-slate-50">
                                    <option value="15">15 Minutes</option>
                                    <option value="30">30 Minutes</option>
                                    <option value="60" selected>60 Minutes</option>
                                    <option value="90">90 Minutes</option>
                                    <option value="120">2 Hours</option>
                                </select>
                            </div>

                            {{-- Weekly Schedule --}}
                            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                                <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                                    <h2 class="font-bold text-slate-800">Weekly Availability</h2>
                                    <button type="button" @click="showBulk = !showBulk" class="text-sm text-indigo-600 font-bold hover:text-indigo-700 bg-indigo-50 px-4 py-2 rounded-lg">Bulk Edit</button>
                                </div>
                                
                                {{-- Bulk Edit --}}
                                <div x-show="showBulk" x-transition class="bg-indigo-50/80 p-4 border-b border-indigo-100 flex items-center gap-4">
                                    <span class="text-xs font-bold text-indigo-800 uppercase">Set all to:</span>
                                    <input type="time" x-model="bulkStart" class="border-none rounded p-1 text-xs font-bold">
                                    <span>?</span>
                                    <input type="time" x-model="bulkEnd" class="border-none rounded p-1 text-xs font-bold">
                                    <button type="button" @click="applyBulkTime()" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-indigo-700">Apply</button>
                                </div>

                                <div class="divide-y divide-slate-100">
                                    <template x-for="day in days" :key="day.key">
                                        <div class="flex items-center justify-between p-5 hover:bg-slate-50 transition-colors group">
                                            <div class="flex items-center gap-5 w-48">
                                                <div class="relative inline-block w-12 h-7">
                                                    <input type="checkbox" :name="`operating_hours[${day.key}][enabled]`" :id="`toggle-${day.key}`" x-model="schedule[day.key].enabled" value="1" class="toggle-checkbox absolute block w-7 h-7 rounded-full bg-white border-4 appearance-none cursor-pointer border-slate-200 checked:right-0 checked:border-indigo-600"/>
                                                    <label :for="`toggle-${day.key}`" class="toggle-label block overflow-hidden h-7 rounded-full bg-slate-200 cursor-pointer"></label>
                                                </div>
                                                <label :for="`toggle-${day.key}`" class="text-sm font-bold text-slate-700" x-text="day.name"></label>
                                            </div>
                                            <div class="flex-1 flex justify-end">
                                                <div x-show="schedule[day.key].enabled" class="flex items-center gap-3 bg-white border border-slate-200 rounded-lg px-3 py-1.5">
                                                    <input type="time" :name="`operating_hours[${day.key}][start]`" x-model="schedule[day.key].start" class="border-none p-0 text-sm font-bold text-slate-700 focus:ring-0">
                                                    <span class="text-slate-300 text-xs">to</span>
                                                    <input type="time" :name="`operating_hours[${day.key}][end]`" x-model="schedule[day.key].end" class="border-none p-0 text-sm font-bold text-slate-700 focus:ring-0">
                                                </div>
                                                <div x-show="!schedule[day.key].enabled" class="text-xs font-bold text-slate-400 py-2 px-5 bg-slate-100 rounded-lg uppercase">Closed</div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Block Dates --}}
                        <div class="lg:col-span-1 space-y-6">
                            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6 sticky top-24">
                                <h2 class="font-bold text-slate-800 mb-2">Block Dates</h2>
                                <p class="text-xs text-slate-500 mb-6">Select specific dates when unavailable.</p>
                                <input type="text" id="unavailableDates" name="unavailable_dates" class="w-full pl-10 rounded-xl border-slate-200 focus:ring-indigo-500 text-sm py-3 font-medium shadow-sm" placeholder="Select dates...">
                                <div class="grid grid-cols-2 gap-3 mt-4">
                                    <button type="button" onclick="quickBlockDates(1, 'week')" class="px-3 py-2 bg-slate-50 text-slate-600 text-xs rounded-lg font-bold border border-slate-200">+ 1 Week</button>
                                    <button type="button" onclick="quickBlockDates(1, 'month')" class="px-3 py-2 bg-slate-50 text-slate-600 text-xs rounded-lg font-bold border border-slate-200">+ 1 Month</button>
                                    <button type="button" onclick="clearUnavailableDates()" class="col-span-2 px-3 py-2 bg-rose-50 text-rose-600 text-xs rounded-lg font-bold border border-rose-100">Clear All</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 rounded-xl p-8 text-center border border-gray-100 mt-8">
                    <h3 class="text-lg font-bold text-gray-900">Ready to Launch?</h3>
                    <div class="flex justify-center gap-4 mt-6">
                        <button type="button" onclick="nextStep('availability', 'description')" class="px-5 py-3 text-gray-600 hover:bg-gray-100 rounded-lg font-medium">Review Details</button>
                        <button type="button" onclick="submitForm()" class="px-8 py-3 bg-green-600 text-white font-bold rounded-lg hover:bg-green-700 shadow-lg">Publish Service Now</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // --- 1. QUILL CONFIG ---
    const toolbarOptions = [['bold', 'italic', 'underline'], [{'list': 'bullet'}]];
    function setupQuill(editorId, inputId, placeholder) {
        var quill = new Quill('#' + editorId, { theme: 'snow', modules: { toolbar: toolbarOptions }, placeholder: placeholder });
        quill.on('text-change', function() { document.getElementById(inputId).value = quill.root.innerHTML; });
    }
    document.addEventListener('DOMContentLoaded', function() {
        setupQuill('editor-basic', 'input-basic', 'e.g. 1 hour online consultation...');
        setupQuill('editor-standard', 'input-standard', 'Describe standard package...');
        setupQuill('editor-premium', 'input-premium', 'Describe premium package...');
        setupQuill('editor-main', 'input-main', 'Provide a comprehensive description...');
    });

    // --- 2. ALPINE JS SCHEDULER ---
    function scheduleHandler() {
        return {
            isSessionBased: true,
            days: [
                { key: 'mon', name: 'Monday' }, { key: 'tue', name: 'Tuesday' },
                { key: 'wed', name: 'Wednesday' }, { key: 'thu', name: 'Thursday' },
                { key: 'fri', name: 'Friday' }, { key: 'sat', name: 'Saturday' },
                { key: 'sun', name: 'Sunday' }
            ],
            // Inject PHP Default Schedule here
            schedule: @json($defaultSchedule),
            showBulk: false,
            bulkStart: '09:00',
            bulkEnd: '17:00',
            applyBulkTime() {
                for (const dayKey in this.schedule) {
                    if (this.schedule[dayKey].enabled) {
                        this.schedule[dayKey].start = this.bulkStart;
                        this.schedule[dayKey].end = this.bulkEnd;
                    }
                }
                this.showBulk = false;
            }
        }
    }

    // --- 3. FLATPICKR ---
    let fpInstance;
    document.addEventListener('DOMContentLoaded', function() {
        fpInstance = flatpickr("#unavailableDates", {
            mode: "multiple", dateFormat: "Y-m-d", minDate: "today", conjunction: ", ",
            locale: { firstDayOfWeek: 1 }
        });
    });
    window.quickBlockDates = function(amount, unit) {
        if (!fpInstance) return;
        let daysToAdd = unit === 'week' ? amount * 7 : amount * 30;
        let newDates = [], today = new Date();
        for (let i = 0; i < daysToAdd; i++) {
            let d = new Date(today); d.setDate(today.getDate() + i);
            newDates.push(d.toISOString().split('T')[0]);
        }
        let current = fpInstance.selectedDates.map(d => d.toISOString().split('T')[0]);
        fpInstance.setDate([...new Set([...current, ...newDates])], true);
    };
    window.clearUnavailableDates = function() { if (fpInstance) fpInstance.clear(); };

    // --- 4. EXTRA PACKAGES UI ---
    document.getElementById('offer_packages').addEventListener('change', function() {
        const extra = document.getElementById('extraPackages');
        this.checked ? extra.classList.remove('hidden') : extra.classList.add('hidden');
    });

    // --- 5. NAVIGATION ---
    function nextStep(currentId, nextId) {
        // Validation (simplified for brevity)
        if (currentId === 'overview' && nextId === 'pricing' && (!document.getElementById('title').value || !document.getElementById('category_id').value)) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please enter a Title and Category.' }); return;
        }
        if (currentId === 'pricing' && nextId === 'description' && !document.getElementById('basic_price').value) {
            Swal.fire({ icon: 'warning', title: 'Missing Info', text: 'Please set a price for the Basic Package.' }); return;
        }
        
        // Tab switching
        document.querySelectorAll('.tab-section').forEach(el => el.classList.add('hidden'));
        document.getElementById(nextId).classList.remove('hidden');
        updateHeader(nextId);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function updateHeader(activeId) {
        const map = { 'overview': 0, 'pricing': 1, 'description': 2, 'availability': 3 };
        const activeIndex = map[activeId];
        document.querySelectorAll('.step-link').forEach((link, index) => {
            const circle = link.querySelector('span');
            // Reset classes
            link.className = "step-link w-1/4 py-4 px-1 border-b-2 font-medium text-sm flex items-center justify-center transition-colors pointer-events-none";
            circle.className = "w-6 h-6 rounded-full flex items-center justify-center text-xs mr-2 font-bold";
            
            if (index < activeIndex) {
                link.classList.add('step-completed', 'border-green-500', 'text-green-600');
                circle.classList.add('bg-green-100', 'text-green-600');
                circle.innerHTML =  "✓";
            } else if (index === activeIndex) {
                link.classList.add('step-active', 'border-indigo-500', 'text-indigo-600');
                circle.classList.add('bg-indigo-100', 'text-indigo-600', 'ring-1', 'ring-indigo-600');
                circle.innerHTML = index + 1;
            } else {
                link.classList.add('step-inactive', 'border-transparent', 'text-gray-400');
                circle.classList.add('bg-gray-100', 'text-gray-500');
                circle.innerHTML = index + 1;
            }
        });
    }

    // --- 6. SUBMISSION ---
    async function submitForm() {
        const form = document.getElementById('createServiceForm');
        // Sync Flatpickr
        if (fpInstance) document.getElementById('unavailableDates').value = fpInstance.input.value;

        const formData = new FormData(form);
        
        Swal.fire({ title: 'Publishing...', didOpen: () => Swal.showLoading() });

        try {
            const response = await fetch("{{ route('services.store') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                Swal.fire({
                    icon: 'success', title: 'Success!', text: 'Service published successfully.',
                    confirmButtonText: 'Go to Dashboard', confirmButtonColor: '#10b981'
                }).then(() => { window.location.href = "{{ route('services.manage') }}"; });
            } else {
                // Show validation errors or generic error
                let msg = data.error || 'Please check your inputs.';
                if(data.errors) { msg = Object.values(data.errors).flat().join('\n'); }
                Swal.fire('Error', msg, 'error');
            }
        } catch (error) {
            console.error(error);
            Swal.fire('System Error', 'Please check your connection.', 'error');
        }
    }
</script>
@endsection