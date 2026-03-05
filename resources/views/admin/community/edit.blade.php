@extends('admin.layout')

@section('content')
<div class="px-4 sm:px-6">

<div class="max-w-4xl mx-auto">

    <!-- Back -->
    <a href="{{ route('admin.community.view', $user->hu_id) }}" 
       class="hover:text-cyan-400 text-sm mb-6 inline-block transition-colors duration-300"
       style="color: var(--text-secondary);">
        ← Back to Profile
    </a>

    <h1 class="text-3xl font-bold mb-6 transition-colors duration-300" style="color: var(--text-primary);">Edit Community User</h1>

    <div class="shadow-xl rounded-xl p-8 border transition-all duration-300"
         style="background-color: var(--bg-secondary); border-color: var(--border-color);">

        <form action="{{ route('admin.community.update', $user->hu_id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- SECTION: PROFILE PHOTO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">Profile Photo</h2>

                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                <div class="h-24 w-24 rounded-full overflow-hidden border shadow transition-colors duration-300"
                     style="border-color: var(--border-color);">
                    <img src="{{ $user->profile_image_url }}" class="w-full h-full object-cover rounded-full" alt="Profile" />
                </div>
                    <input type="file" name="profile_photo" 
                           class="border p-2 rounded-lg w-full text-sm transition-colors duration-300"
                           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);">
                </div>
            </div>

            <!-- SECTION: BASIC INFO -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">Basic Information</h2>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">Name</label>
                    <input type="text" name="name" value="{{ old('name', $user->hu_name) }}"
                           class="border p-3 rounded-lg w-full transition-colors duration-300"
                           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);" required>
                </div>

                <!-- Email -->
                <div class="mb-4">
                    <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->hu_email) }}"
                           class="border p-3 rounded-lg w-full transition-colors duration-300"
                           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);" required>
                </div>

                <!-- Phone -->
                <div class="mb-4">
                    <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">Phone</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->hu_phone) }}"
                           class="border p-3 rounded-lg w-full transition-colors duration-300"
                           style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);">
                </div>

                <div class="mb-4">
                    <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">Bio</label>
                    <textarea name="bio" rows="3"
                              class="border p-3 rounded-lg w-full transition-colors duration-300"
                              style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Optional bio">{{ old('bio', $user->hu_bio) }}</textarea>
                </div>
            </div>            

            <!-- SECTION: VERIFICATION -->
            <div class="mb-8">
                <h2 class="text-xl font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">Verification Status</h2>

                <select name="verification_status"
        class="border p-3 rounded-lg w-full transition-colors duration-300"
        style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);">

    {{-- Pending (disabled if already approved) --}}
    <option value="pending"
        {{ old('verification_status', $user->hu_verification_status) == 'pending' ? 'selected' : '' }}
        {{ $user->hu_verification_status == 'approved' ? 'disabled' : '' }}>
        Pending
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

@if($user->hu_verification_status === 'approved')
    <p class="text-xs mt-2 transition-colors duration-300" style="color: var(--text-muted);">
        Verified users cannot be reverted back to Pending.
    </p>
@endif

            </div>

            <!-- SECTION: BLACKLIST -->
            <div class="mb-10">
                <h2 class="text-xl font-semibold mb-3 transition-colors duration-300" style="color: var(--text-primary);">Blacklist Status</h2>

                @if($user->hu_is_blacklisted)
                    <div class="p-4 bg-red-100 border border-red-300 rounded-lg mb-4">
                        <p class="text-red-700 font-semibold">This user is currently blacklisted.</p>
                        <p class="text-red-700 text-sm mt-1"><strong>Reason:</strong> {{ $user->hu_blacklist_reason }}</p>
                    </div>

                    <label class="flex items-center gap-2 font-medium transition-colors duration-300" style="color: var(--text-primary);">
                        <input type="checkbox" name="remove_blacklist" value="1">
                        Remove blacklist
                    </label>

                @else
                    <label class="block font-medium mb-1 transition-colors duration-300" style="color: var(--text-primary);">
                        Add blacklist reason (optional)
                    </label>

                    <textarea name="blacklist_reason" rows="3"
                              class="border p-3 rounded-lg w-full transition-colors duration-300"
                              style="background-color: var(--bg-tertiary); border-color: var(--border-color); color: var(--text-primary);"
                              placeholder="Enter reason to blacklist this user...">{{ old('blacklist_reason') }}</textarea>
                @endif
            </div>

            <!-- SUBMIT BUTTON -->
            <button class="px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white rounded-lg shadow transition-all duration-300">
                <i class="fa-solid fa-save mr-2"></i> Save Changes
            </button>

        </form>
    </div>
</div>

</div>
@endsection
