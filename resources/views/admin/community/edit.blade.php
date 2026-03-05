@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

<div class="max-w-5xl mx-auto">

    {{-- Back Navigation --}}
    <div class="mb-8">
        <a href="{{ route('admin.community.view', $user->hu_id) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl transition-all duration-300 hover:shadow-lg"
           style="background-color: var(--bg-secondary); color: var(--text-secondary); border: 1px solid var(--border-color);">
            <i class="fas fa-arrow-left"></i>
            <span class="font-medium">Back to Profile</span>
        </a>
    </div>

    {{-- Page Header --}}
    <div class="rounded-xl shadow-xl border transition-all duration-300 mb-8" 
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">
        
        {{-- Header --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-700 px-6 py-6 rounded-t-xl">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-edit text-white text-2xl"></i>
                </div>
                <div>
                    <h1 class="text-white font-bold text-2xl">Edit Community User</h1>
                    <p class="text-blue-100 text-sm">Manage user profile and verification settings</p>
                </div>
            </div>
        </div>
        
        {{-- User Info Bar --}}
        <div class="px-6 py-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-blue-200">
                    <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover" alt="Profile" />
                </div>
                <div>
                    <h2 class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">{{ $user->hu_name }}</h2>
                    <p class="text-sm transition-colors duration-300" style="color: var(--text-muted);">ID: {{ $user->hu_id }} • {{ $user->hu_email }}</p>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('admin.community.update', $user->hu_id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid gap-8">

            {{-- PROFILE PHOTO SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-purple-600 to-pink-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-camera text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Profile Photo</h2>
                            <p class="text-purple-100 text-sm">Update user avatar image</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        <div class="relative">
                            <div class="h-24 w-24 rounded-xl overflow-hidden border-2 shadow-lg transition-colors duration-300"
                                 style="border-color: var(--border-color);">
                                <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover" alt="Profile" />
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-6 h-6 bg-green-500 rounded-full border-2 border-white flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                        </div>
                        
                        <div class="flex-1">
                            <label class="block text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">Choose New Photo</label>
                            <input type="file" name="profile_photo" 
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                            <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Accepted formats: JPG, PNG, GIF (max 2MB)</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BASIC INFORMATION SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-emerald-600 to-teal-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-user-alt text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Basic Information</h2>
                            <p class="text-emerald-100 text-sm">Personal details and contact information</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        
                        {{-- Name Field --}}
                        <div class="lg:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-user text-blue-500"></i>
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" value="{{ old('name', $user->hu_name) }}"
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                                   required>
                        </div>

                        {{-- Email Field --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-envelope text-purple-500"></i>
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" value="{{ old('email', $user->hu_email) }}"
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);" 
                                   required>
                        </div>

                        {{-- Phone Field --}}
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-phone text-green-500"></i>
                                Phone Number
                            </label>
                            <input type="text" name="phone" value="{{ old('phone', $user->hu_phone) }}"
                                   class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent"
                                   style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">
                        </div>

                        {{-- Bio Field --}}
                        <div class="lg:col-span-2">
                            <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-comment-alt text-amber-500"></i>
                                Bio
                            </label>
                            <textarea name="bio" rows="4"
                                      class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-emerald-500 focus:border-transparent resize-none"
                                      style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                      placeholder="Tell us about yourself...">{{ old('bio', $user->hu_bio) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>            

            {{-- VERIFICATION STATUS SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-indigo-600 to-purple-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-shield-check text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Verification Status</h2>
                            <p class="text-indigo-100 text-sm">Manage user account verification level</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    <div class="space-y-6">
                        <div>
                            <label class="flex items-center gap-2 text-sm font-semibold mb-4 transition-colors duration-300" style="color: var(--text-primary);">
                                <i class="fas fa-clipboard-check text-indigo-500"></i>
                                Current Status: 
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold
                                    {{ $user->hu_verification_status == 'approved' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $user->hu_verification_status == 'pending' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $user->hu_verification_status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ ucfirst($user->hu_verification_status) }}
                                </span>
                            </label>
                            
                            <select name="verification_status"
                                    class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                    style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);">

                                {{-- Pending (disabled if already approved) --}}
                                <option value="pending"
                                    {{ old('verification_status', $user->hu_verification_status) == 'pending' ? 'selected' : '' }}
                                    {{ $user->hu_verification_status == 'approved' ? 'disabled' : '' }}>
                                    Pending Verification
                                </option>

                                {{-- Approved --}}
                                <option value="approved"
                                    {{ old('verification_status', $user->hu_verification_status) == 'approved' ? 'selected' : '' }}>
                                    Approved
                                </option>

                                {{-- Rejected --}}
                                <option value="rejected"
                                    {{ old('verification_status', $user->hu_verification_status) == 'rejected' ? 'selected' : '' }}>
                                    Rejected
                                </option>
                            </select>
                        </div>
                        
                        @if($user->hu_verification_status === 'approved')
                            <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-info-circle text-blue-600"></i>
                                    <p class="text-sm font-medium text-blue-800">
                                        Verified users cannot be reverted back to Pending status.
                                    </p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- BLACKLIST STATUS SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                {{-- Header --}}
                <div class="bg-gradient-to-r from-red-600 to-pink-700 px-6 py-4 rounded-t-xl">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ban text-white text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-white font-bold text-xl">Blacklist Status</h2>
                            <p class="text-red-100 text-sm">Manage user account restrictions</p>
                        </div>
                    </div>
                </div>

                <div class="p-6 sm:p-8">
                    @if($user->hu_is_blacklisted)
                        <div class="space-y-6">
                            {{-- Current Blacklist Alert --}}
                            <div class="p-6 bg-red-50 border border-red-200 rounded-xl">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-exclamation-triangle text-red-600 text-xl"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h3 class="font-bold text-red-800 text-lg mb-2">This user is currently blacklisted</h3>
                                        <div class="bg-white p-4 rounded-lg border border-red-100">
                                            <p class="text-sm font-semibold text-red-700 mb-1">Blacklist Reason:</p>
                                            <p class="text-red-800">{{ $user->hu_blacklist_reason }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Remove Blacklist Option --}}
                            <div class="p-6 bg-green-50 border border-green-200 rounded-xl">
                                <label class="flex items-center gap-4 cursor-pointer">
                                    <input type="checkbox" name="remove_blacklist" value="1" 
                                           class="w-5 h-5 text-green-600 border-2 border-green-300 rounded focus:ring-green-500">
                                    <div class="flex-1">
                                        <span class="font-semibold text-green-800">Remove blacklist status</span>
                                        <p class="text-sm text-green-600 mt-1">This will restore the user's account access</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    @else
                        <div class="space-y-6">
                            {{-- Status Display --}}
                            <div class="p-6 bg-green-50 border border-green-200 rounded-xl">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-check-circle text-green-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-green-800">User account is in good standing</h3>
                                        <p class="text-green-600 text-sm">No blacklist restrictions currently applied</p>
                                    </div>
                                </div>
                            </div>

                            {{-- Add Blacklist Option --}}
                            <div>
                                <label class="flex items-center gap-2 text-sm font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">
                                    <i class="fas fa-gavel text-red-500"></i>
                                    Add blacklist reason (optional)
                                </label>
                                <textarea name="blacklist_reason" rows="4"
                                          class="w-full px-4 py-3 rounded-xl border transition-all duration-300 focus:ring-2 focus:ring-red-500 focus:border-transparent resize-none"
                                          style="background-color: var(--bg-primary); border-color: var(--border-color); color: var(--text-primary);"
                                          placeholder="Enter a detailed reason for blacklisting this user...">{{ old('blacklist_reason') }}</textarea>
                                <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">Leave empty to keep user active. Adding a reason will blacklist the user.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SUBMIT SECTION --}}
            <div class="rounded-xl shadow-xl border transition-all duration-300" 
                 style="background-color: var(--bg-secondary); border-color: var(--border-color);">
                
                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row gap-4 justify-between items-center">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-save text-blue-600 text-xl"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg transition-colors duration-300" style="color: var(--text-primary);">Ready to save changes?</h3>
                                <p class="text-sm transition-colors duration-300" style="color: var(--text-muted);">Review your changes before updating the user profile</p>
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <a href="{{ route('admin.community.view', $user->hu_id) }}"
                               class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 font-semibold">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                            <button type="submit" 
                                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white rounded-xl shadow-lg transition-all duration-300 flex items-center gap-2 font-semibold">
                                <i class="fas fa-save"></i> Save Changes
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </form>
</div>

</div>
@endsection
